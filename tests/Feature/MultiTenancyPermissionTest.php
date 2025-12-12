<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Company;
use App\Models\Url;
use Spatie\Permission\PermissionRegistrar;

class MultiTenancyPermissionTest extends TestCase
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

    public function test_admin_cannot_view_urls_of_other_company_on_index()
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $adminA = User::factory()->create(['company_id' => $companyA->id]);
        $adminA->assignRole('Admin');

        $urlB = Url::factory()->create([
            'company_id' => $companyB->id,
            'original_url' => 'https://other-company.example/test',
        ]);

        $this->actingAs($adminA)
            ->get(route('urls.index'))
            ->assertStatus(200)
            ->assertDontSee('https://other-company.example/test');
    }

    public function test_superadmin_can_view_any_company_url_on_index()
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $super = User::factory()->create(['company_id' => $companyA->id]);
        $super->assignRole('SuperAdmin');

        $urlB = Url::factory()->create([
            'company_id' => $companyB->id,
            'original_url' => 'https://other-company.example/test2',
        ]);

        $this->actingAs($super)
            ->get(route('urls.index'))
            ->assertStatus(200)
            ->assertSee('https://other-company.example/test2');
    }
}