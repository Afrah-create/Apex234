<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecureSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            $sessionId = Session::getId();
            
            // Generate session fingerprint
            $fingerprint = $this->generateSessionFingerprint($request);
            
            // Store fingerprint in session
            if (!Session::has('session_fingerprint')) {
                Session::put('session_fingerprint', $fingerprint);
            }
            
            // Validate session fingerprint
            if (Session::get('session_fingerprint') !== $fingerprint) {
                Log::warning('Session fingerprint mismatch detected', [
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                // Invalidate session and redirect to login
                Auth::logout();
                Session::flush();
                return redirect()->route('login')->with('error', 'Session security violation detected. Please login again.');
            }
            
            // Check for session hijacking indicators
            if ($this->detectSessionHijacking($request)) {
                Log::alert('Potential session hijacking detected', [
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                Auth::logout();
                Session::flush();
                return redirect()->route('login')->with('error', 'Security violation detected. Please login again.');
            }
            
            // Update last activity
            Session::put('last_activity', time());
        }
        
        return $next($request);
    }
    
    /**
     * Generate a unique session fingerprint
     */
    private function generateSessionFingerprint(Request $request): string
    {
        $components = [
            $request->ip(),
            $request->userAgent(),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
        ];
        
        return hash('sha256', implode('|', array_filter($components)));
    }
    
    /**
     * Detect potential session hijacking
     */
    private function detectSessionHijacking(Request $request): bool
    {
        // Check for suspicious IP changes
        $currentIp = $request->ip();
        $storedIp = Session::get('original_ip');
        
        if (!$storedIp) {
            Session::put('original_ip', $currentIp);
            return false;
        }
        
        // Allow for proxy/CDN IP changes but log them
        if ($currentIp !== $storedIp) {
            Log::info('IP address changed during session', [
                'original_ip' => $storedIp,
                'current_ip' => $currentIp,
                'user_agent' => $request->userAgent()
            ]);
        }
        
        return false; // You can implement more sophisticated detection here
    }
}
