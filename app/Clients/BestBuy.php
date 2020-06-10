<?php

namespace App\Clients;

use App\Stock;
use Illuminate\Support\Facades\Http;

class BestBuy implements Client
{
    public function checkAvailability(Stock $stock): StockStatus
    {
        $result = Http::get($this->endpoint($stock->sku))->json();

        return new StockStatus(
            $result['onlineAvailability'],
            (int) $result['salePrice'] * 100
        );
    }

    protected function endpoint($sku): string
    {
        $apiKey = config('services.clients.bestBuy.key');

        return "https://api.bestbuy.com/v1/products/{$sku}.json?apiKey={$apiKey}";
    }

}
