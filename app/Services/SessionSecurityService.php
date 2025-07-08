<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SessionSecurityService
{
    /**
     * Regenerate session ID for security
     */
    public function regenerateSession(): void
    {
        Session::regenerate();
        Log::info('Session regenerated for security', [
            'user_id' => Auth::id(),
            'new_session_id' => Session::getId()
        ]);
    }
    
    /**
     * Invalidate all sessions for a user (except current)
     */
    public function invalidateOtherSessions(int $userId): void
    {
        $currentSessionId = Session::getId();
        
        // Delete other sessions from database
        DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', $currentSessionId)
            ->delete();
            
        Log::info('Invalidated other sessions for user', [
            'user_id' => $userId,
            'current_session_id' => $currentSessionId
        ]);
    }
    
    /**
     * Get active sessions for a user
     */
    public function getActiveSessions(int $userId): array
    {
        return DB::table('sessions')
            ->where('user_id', $userId)
            ->where('last_activity', '>', time() - (config('session.lifetime') * 60))
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => date('Y-m-d H:i:s', $session->last_activity),
                    'is_current' => $session->id === Session::getId()
                ];
            })
            ->toArray();
    }
    
    /**
     * Force logout user from all devices
     */
    public function forceLogoutAllDevices(int $userId): void
    {
        // Delete all sessions for the user
        DB::table('sessions')
            ->where('user_id', $userId)
            ->delete();
            
        Log::warning('Force logout all devices for user', [
            'user_id' => $userId,
            'initiated_by' => Auth::id()
        ]);
    }
    
    /**
     * Check if session is suspicious
     */
    public function isSuspiciousSession(Request $request): bool
    {
        $suspicious = false;
        $reasons = [];
        
        // Check for missing or changed fingerprint
        if (!Session::has('session_fingerprint')) {
            $suspicious = true;
            $reasons[] = 'Missing session fingerprint';
        }
        
        // Check for rapid session changes
        $lastRegeneration = Session::get('last_regeneration', 0);
        if (time() - $lastRegeneration < 60) { // Less than 1 minute
            $suspicious = true;
            $reasons[] = 'Rapid session regeneration';
        }
        
        // Check for multiple failed login attempts
        $failedAttempts = Session::get('failed_login_attempts', 0);
        if ($failedAttempts > 5) {
            $suspicious = true;
            $reasons[] = 'Multiple failed login attempts';
        }
        
        if ($suspicious) {
            Log::warning('Suspicious session detected', [
                'user_id' => Auth::id(),
                'reasons' => $reasons,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }
        
        return $suspicious;
    }
    
    /**
     * Record failed login attempt
     */
    public function recordFailedLoginAttempt(): void
    {
        $attempts = Session::get('failed_login_attempts', 0);
        Session::put('failed_login_attempts', $attempts + 1);
        
        // Reset attempts after 15 minutes
        if ($attempts === 0) {
            Session::put('failed_login_reset_time', time() + 900);
        }
    }
    
    /**
     * Reset failed login attempts
     */
    public function resetFailedLoginAttempts(): void
    {
        Session::forget(['failed_login_attempts', 'failed_login_reset_time']);
    }
    
    /**
     * Clean up expired sessions
     */
    public function cleanupExpiredSessions(): int
    {
        $lifetime = config('session.lifetime') * 60;
        $cutoff = time() - $lifetime;
        
        $deleted = DB::table('sessions')
            ->where('last_activity', '<', $cutoff)
            ->delete();
            
        Log::info('Cleaned up expired sessions', ['count' => $deleted]);
        
        return $deleted;
    }
} 