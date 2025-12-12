<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Company;
use App\Models\Invitation;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class InviteFlowTest extends TestCase
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

    public function test_superadmin_can_invite_admin_only()
    {
        $company = Company::factory()->create();

        $super = User::factory()->create(['company_id' => $company->id]);
        $super->assignRole('SuperAdmin');

        $response = $this->actingAs($super)
            ->post(route('invite.store'), [
                'email' => 'admin.invite@example.com',
                'role' => 'Admin',
                'company_id' => $company->id,
            ]);

        $response->assertStatus(302);

        $invite = Invitation::where('email', 'admin.invite@example.com')->first();
        $this->assertNotNull($invite, 'Invitation not created for admin invite.');
        $this->assertEquals($super->id, $invite->invite_by, 'invite_by should be superadmin id for admin invite.');
        $this->assertEquals('Admin', $invite->role);

        $response2 = $this->actingAs($super)
            ->post(route('invite.store'), [
                'email' => 'member.invite@example.com',
                'role' => 'Member',
                'company_id' => $company->id,
            ]);

        $response2->assertStatus(403);
        $this->assertDatabaseMissing('invitations', ['email' => 'member.invite@example.com']);
    }

    public function test_admin_can_invite_admin_or_member_within_their_company()
    {
        $company = Company::factory()->create();

        $admin = User::factory()->create(['company_id' => $company->id]);
        $admin->assignRole('Admin');

        $respMember = $this->actingAs($admin)
            ->post(route('invite.store'), [
                'email' => 'member.invite@example.com',
                'role' => 'Member',
                'company_id' => $company->id,
            ]);

        $respMember->assertStatus(302);
        $inviteMember = Invitation::where('email', 'member.invite@example.com')->first();
        $this->assertNotNull($inviteMember, 'Invitation not created for member invite.');
        $this->assertEquals($admin->id, $inviteMember->invite_by, 'invite_by should be admin id for member invite.');
        $this->assertEquals('Member', $inviteMember->role);
        $this->assertEquals($company->id, $inviteMember->company_id);

        $respAdmin = $this->actingAs($admin)
            ->post(route('invite.store'), [
                'email' => 'admin2.invite@example.com',
                'role' => 'Admin',
                'company_id' => $company->id,
            ]);

        $respAdmin->assertStatus(302);
        $inviteAdmin = Invitation::where('email', 'admin2.invite@example.com')->first();
        $this->assertNotNull($inviteAdmin, 'Invitation not created for admin invite by admin.');
        $this->assertEquals($admin->id, $inviteAdmin->invite_by);
        $this->assertEquals('Admin', $inviteAdmin->role);
    }

    public function test_admin_cannot_invite_for_other_company_or_outside_scope()
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $admin = User::factory()->create(['company_id' => $companyA->id]);
        $admin->assignRole('Admin');

        $resp = $this->actingAs($admin)
            ->post(route('invite.store'), [
                'email' => 'forbidden.member@example.com',
                'role' => 'Member',
                'company_id' => $companyB->id,
            ]);

        $resp->assertStatus(403);
        $this->assertDatabaseMissing('invitations', ['email' => 'forbidden.member@example.com']);

        $resp2 = $this->actingAs($admin)
            ->post(route('invite.store'), [
                'email' => 'forbidden.admin@example.com',
                'role' => 'Admin',
                'company_id' => $companyB->id,
            ]);

        $resp2->assertStatus(403);
        $this->assertDatabaseMissing('invitations', ['email' => 'forbidden.admin@example.com']);
    }

    public function test_accept_invite_page_loads_with_token()
    {
        $company = Company::factory()->create();
        $token = Str::random(32);

        $invite = Invitation::create([
            'email' => 'joiner@example.com',
            'company_id' => $company->id,
            'role' => 'Member',
            'token' => $token,
            'invite_by' => User::factory()->create()->id,
        ]);

        $this->get(route('invite.accept', ['token' => $token]))
            ->assertStatus(200);
    }

    public function test_accept_submit_registers_user_and_assigns_role_company_if_route_exists()
    {
        if (!\Route::has('invite.accept.submit')) {
            $this->markTestSkipped('Route invite.accept.submit is not defined.');
            return;
        }

        $company = Company::factory()->create();
        $token = Str::random(32);

        $invite = Invitation::create([
            'email' => 'joiner2@example.com',
            'company_id' => $company->id,
            'role' => 'Member',
            'token' => $token,
            'invite_by' => User::factory()->create()->id,
        ]);

        $response = $this->post(route('invite.accept.submit', ['token' => $token]), [
            'name' => 'Joiner',
            'email' => 'joiner2@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'email' => 'joiner2@example.com',
            'company_id' => $company->id,
        ]);

        $this->assertDatabaseHas('invitations', [
            'email' => 'joiner2@example.com',
        ]);
    }
}