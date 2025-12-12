<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Company;
use App\Models\Url;
use Spatie\Permission\PermissionRegistrar;

class ShortUrlCreateTest extends TestCase
{
    // use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (class_exists(\Database\Seeders\RolesTableSeeder::class)) {
            $this->seed(\Database\Seeders\RolesTableSeeder::class);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_authenticated_user_can_create_short_url_and_it_has_company_and_user()
    {
        $company = Company::factory()->create();

        $user = User::factory()->create([
            'company_id' => $company->id,
        ]);

        $user->assignRole('Member');

        $response = $this->actingAs($user)
            ->post(route('urls.store'), [
                'original_url' => 'https://example.com/long/path',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('urls', [
            'original_url' => 'https://example.com/long/path',
            'company_id' => $company->id,
            'created_by' => $user->id,
        ]);
    }

    public function test_invalid_url_is_rejected()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('Member');

        $response = $this->actingAs($user)
            ->post(route('urls.store'), ['original_url' => 'invalid-url']);

        $response->assertSessionHasErrors('original_url');
    }

    public function test_guest_cannot_create_short_url()
    {
        $this->post(route('urls.store'), ['original_url' => 'https://example.com'])
            ->assertRedirect(route('login'));
    }

    public function test_public_short_url_is_publicly_resolvable_and_redirects()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        $short = Url::factory()->create([
            'original_url' => 'https://example.com/some-page',
            'short_code' => fake()->unique()->regexify('[A-Za-z0-9]{10}'),
            'company_id' => $company->id,
            'created_by' => $user->id,
        ]);

        $response = $this->get('u/' . $short->short_code);

        $response->assertStatus(302);
        $response->assertRedirect('https://example.com/some-page');
    }
}