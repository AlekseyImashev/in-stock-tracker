<?php

namespace Tests\Integration;

use App\Stock;
use App\History;
use Tests\TestCase;
use App\UseCases\TrackStock;
use RetailerWithProductSeeder;
use App\Notifications\ImportantStockUpdate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackStockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->mockClientRequest($available = true, $price = 24900);

        $this->seed(RetailerWithProductSeeder::class);

        (new TrackStock(Stock::first()))->handle();
    }


    /** @test */
    function it_notify_the_user()
    {
        Notification::assertTimesSent(1, ImportantStockUpdate::class);
    }

    /** @test */
    function it_refreshes_the_local_stock()
    {
        tap(Stock::first(), function ($stock) {
            $this->assertEquals(24900, $stock->price);
            $this->assertTrue(true, $stock->in_stock);
        });
    }

    /** @test */
    function it_records_to_history()
    {
        $this->assertEquals(1, History::count());
    }
}
