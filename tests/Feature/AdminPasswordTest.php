<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_non_admins_cannot_open_password_page(): void
    {
        $this->get(route('admin.password.edit'))->assertRedirect(route('login'));

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get(route('admin.password.edit'))->assertForbidden();
        $this->actingAs($user)->put(route('admin.password.update'), [])->assertForbidden();
    }

    public function test_admin_can_change_password_with_current_password(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.password.edit'))
            ->assertOk()
            ->assertSee('Update Your Password');

        $this->actingAs($admin)
            ->put(route('admin.password.update'), [
                'current_password' => 'password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertRedirect(route('admin.password.edit'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('new-secure-password', $admin->fresh()->password));
    }

    public function test_wrong_current_password_or_mismatch_is_rejected(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->put(route('admin.password.update'), [
                'current_password' => 'wrong',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertSessionHasErrors('current_password');

        $this->actingAs($admin)
            ->put(route('admin.password.update'), [
                'current_password' => 'password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'different',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('password', $admin->fresh()->password));
    }
}
