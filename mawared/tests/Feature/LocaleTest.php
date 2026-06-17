<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_locale_is_arabic_with_rtl(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('lang="ar"', false)
            ->assertSee('dir="rtl"', false)
            ->assertSee(__('nav.dashboard', [], 'ar'));
    }

    public function test_authenticated_user_can_switch_to_english(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->get(route('locale.switch', 'en'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('dir="ltr"', false)
            ->assertSee(__('nav.dashboard', [], 'en'));
    }

    public function test_locale_persists_in_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->get(route('locale.switch', 'fr'));

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertSee(__('nav.inventory', [], 'fr'));
    }

    public function test_guest_can_switch_locale_on_login_page(): void
    {
        $this->from(route('login'))
            ->get(route('locale.switch', 'en'))
            ->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertOk()
            ->assertSee(__('auth.login_title', [], 'en'))
            ->assertSee('lang="en"', false);
    }

    public function test_invalid_locale_returns_not_found(): void
    {
        $this->get(route('locale.switch', 'de'))
            ->assertNotFound();
    }

    public function test_role_labels_use_selected_locale(): void
    {
        $user = User::factory()->warehouseKeeper()->create();

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->get(route('locale.switch', 'en'));

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertSee(__('roles.warehouse_keeper', [], 'en'));
    }

    public function test_pagination_uses_selected_locale(): void
    {
        $user = User::factory()->create();
        \App\Models\Asset::factory()->count(16)->create();

        $this->actingAs($user)
            ->from(route('inventory.index'))
            ->get(route('locale.switch', 'fr'));

        $this->actingAs($user)
            ->get(route('inventory.index'))
            ->assertSee('Suivant', false);
    }

    public function test_validation_attributes_use_selected_locale(): void
    {
        app()->setLocale('fr');

        $this->assertSame(
            __('attributes.email', [], 'fr'),
            trans('validation.attributes.email')
        );
    }
}
