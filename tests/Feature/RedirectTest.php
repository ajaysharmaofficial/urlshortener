<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Url;

class RedirectTest extends TestCase
{
    // use RefreshDatabase;

    public function test_redirect_route_increments_hits_and_redirects_to_original()
    {
        $short = Url::factory()->create([
            'original_url' => 'https://laravel.com',
            'short_code' => 'abc123',
            'hits' => 0,
        ]);

        $response = $this->get(route('url.redirect', ['code' => $short->short_code]));

        $response->assertRedirect('https://laravel.com');

        $this->assertDatabaseHas('urls', [
            'id' => $short->id,
            'hits' => 1,
        ]);
    }

    public function test_invalid_code_returns_404()
    {
        $this->get(route('url.redirect', ['code' => 'nope404']))->assertStatus(404);
    }
}