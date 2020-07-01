<?php

namespace Tests\Feature;

use App\Product;
use Tests\TestCase;
use RetailerWithProductSeeder;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackCommendTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_tracks_product_stock()
    {
        parent::setUp();

        Notification::fake();

        $this->seed(RetailerWithProductSeeder::class);

        $this->assertFalse(Product::first()->inStock());

        $this->mockClientRequest();

        $this->artisan('track')
            ->expectsOutput('All Done!');

        $this->assertTrue(Product::first()->inStock());
    }
}
