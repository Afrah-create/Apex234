<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Models\CartItem;
use App\Models\YogurtProduct;
use App\Models\Inventory;
use App\Models\InventoryObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inventory::observe(InventoryObserver::class);
        Event::listen(Login::class, function ($event) {
            $user = $event->user;
            $sessionCart = session('cart', []);
            foreach ($sessionCart as $productId => $item) {
                $cartItem = CartItem::where('user_id', $user->id)->where('product_id', $productId)->first();
                $quantity = $item['quantity'] ?? 1;
                if ($cartItem) {
                    $cartItem->quantity += $quantity;
                    $cartItem->save();
                } else {
                    // Only add if product still exists
                    if (YogurtProduct::find($productId)) {
                        CartItem::create([
                            'user_id' => $user->id,
                            'product_id' => $productId,
                            'quantity' => $quantity,
                        ]);
                    }
                }
            }
            session()->forget('cart');
        });
    }
}
