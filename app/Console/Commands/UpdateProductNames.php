<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\YogurtProduct;

class UpdateProductNames extends Command
{
    protected $signature = 'products:update-names';
    protected $description = 'Update product names for analytics';

    // public function handle()
    // {
    //     $products = YogurtProduct::all();
    //     
    //     foreach ($products as $index => $product) {
    //         $product->update([
    //             'product_name' => 'Yogurt Product ' . ($index + 1)
    //         ]);
    //     }
    //     
    //     $this->info('Updated ' . $products->count() . ' product names.');
    // }
} 