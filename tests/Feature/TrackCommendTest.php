<?php

namespace Tests\Feature;

use App\User;
use App\Product;
use Tests\TestCase;
use App\Clients\StockStatus;
use RetailerWithProductSeeder;
use Facades\App\Clients\ClientFactory;
use App\Notifications\ImportantStockUpdate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackCommendTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_tracks_product_stock()
    {
        $this->seed(RetailerWithProductSeeder::class);

        $this->assertFalse(Product::first()->inStock());

        ClientFactory::shouldReceive('make->checkAvailability')->andReturn(
            new StockStatus($available = true, $price = 29900)
        );

        $this->artisan('track')
            ->expectsOutput('All Done!');

        $this->assertTrue(Product::first()->inStock());
    }

    /** @test */
    function it_does_not_notify_when_the_stock_remains_unavailable()
    {
        Notification::fake();

        $this->seed(RetailerWithProductSeeder::class);

        ClientFactory::shouldReceive('make->checkAvailability')->andReturn(
            new StockStatus($available = false, $price = 29900)
        );

        $this->artisan('track');

        Notification::assertNothingSent();
    }

    /** @test */
    function it_notifies_the_user_when_the_stock_is_now_available()
    {
        Notification::fake();

        $this->seed(RetailerWithProductSeeder::class);

        ClientFactory::shouldReceive('make->checkAvailability')->andReturn(
            new StockStatus($available = true, $price = 29900)
        );

        $this->artisan('track');

        Notification::assertSentTo(User::first(), ImportantStockUpdate::class);
    }
}
