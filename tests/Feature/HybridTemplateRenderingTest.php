<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Package;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HybridTemplateRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_builder_template_without_html_path(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.templates.store'), [
            'name' => 'Builder Aurora',
            'category' => 'wedding',
            'render_mode' => Template::RENDER_MODE_BUILDER,
            'builder_layout' => 'modern-editorial',
            'theme_primary' => '#B76E79',
            'theme_secondary' => '#FFF8F3',
            'theme_accent' => '#7A4E57',
            'theme_background' => '#FFFDFB',
            'theme_text' => '#2B1F24',
            'theme_heading_font' => 'playfair',
            'theme_body_font' => 'jakarta',
            'theme_spacing' => 'comfortable',
            'theme_radius' => 'rounded',
            'supported_event_types' => ['wedding'],
            'section_enabled' => [
                'hero' => 1,
                'couple' => 1,
                'event_schedule' => 1,
                'gallery' => 1,
                'love_story' => 1,
                'gift' => 1,
                'rsvp' => 1,
                'wishes' => 1,
                'map' => 1,
                'footer' => 1,
            ],
            'section_variant' => [
                'hero' => 'cover-split',
                'couple' => 'side-by-side',
                'event_schedule' => 'timeline',
                'gallery' => 'grid',
                'love_story' => 'cards',
                'gift' => 'cards',
                'rsvp' => 'panel',
                'wishes' => 'feed',
                'map' => 'card',
                'footer' => 'signature',
            ],
            'section_order' => [
                'hero' => 1,
                'couple' => 2,
                'event_schedule' => 3,
                'gallery' => 4,
                'love_story' => 5,
                'gift' => 6,
                'rsvp' => 7,
                'wishes' => 8,
                'map' => 9,
                'footer' => 10,
            ],
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.templates.index'));

        $this->assertDatabaseHas('templates', [
            'name' => 'Builder Aurora',
            'render_mode' => Template::RENDER_MODE_BUILDER,
            'builder_layout' => 'modern-editorial',
            'html_path' => null,
        ]);
    }

    public function test_admin_rejects_missing_blade_view_for_legacy_template(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->from(route('admin.templates.create'))
            ->actingAs($admin)
            ->post(route('admin.templates.store'), [
                'name' => 'Broken Legacy',
                'category' => 'wedding',
                'render_mode' => Template::RENDER_MODE_BLADE,
                'html_path' => 'invitations.templates.not-found.index',
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('admin.templates.create'));
        $response->assertSessionHasErrors('html_path');
    }

    public function test_builder_template_demo_uses_universal_builder_renderer(): void
    {
        $template = Template::create([
            'name' => 'Builder Demo',
            'slug' => 'builder-demo',
            'category' => 'wedding',
            'render_mode' => Template::RENDER_MODE_BUILDER,
            'builder_layout' => 'soft-garden',
            'builder_config' => Template::defaultBuilderConfig('wedding'),
            'is_active' => true,
        ]);

        $response = $this->get(route('templates.demo', $template->slug));

        $response->assertOk();
        $response->assertSee('Demo Builder Demo');
        $response->assertSee('Konfirmasi kehadiran', false);
    }

    public function test_public_invitation_renders_builder_template(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $package = Package::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'tier' => 'starter',
            'price' => 0,
            'billing_type' => 'one_time',
            'max_guests' => 100,
            'max_photos' => 10,
            'max_invitations' => 1,
            'features' => [],
            'addons' => [],
            'allowed_template_ids' => [],
            'is_active' => true,
            'is_recommended' => false,
        ]);

        $template = Template::create([
            'name' => 'Builder Public',
            'slug' => 'builder-public',
            'category' => 'wedding',
            'render_mode' => Template::RENDER_MODE_BUILDER,
            'builder_layout' => 'classic-romance',
            'builder_config' => Template::defaultBuilderConfig('wedding'),
            'is_active' => true,
        ]);

        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'package_id' => $package->id,
            'slug' => 'hybrid-public-template',
            'event_type' => 'wedding',
            'title' => 'Hybrid Public Template',
            'groom_name' => 'Raka',
            'bride_name' => 'Alya',
            'event_date' => now()->addDays(14)->toDateString(),
            'event_time' => '09:00',
            'venue_name' => 'Grand Ballroom',
            'venue_address' => 'Jl. Mawar No. 123',
            'status' => 'active',
            'published_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        $response = $this->get(route('invitation.show', $invitation->slug));

        $response->assertOk();
        $response->assertSee('Hybrid Public Template');
        $response->assertSee('Konfirmasi kehadiran', false);
    }
}
