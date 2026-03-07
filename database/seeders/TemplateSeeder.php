<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        Template::create([
            'name' => 'Wedding Elegant',
            'slug' => 'wedding-elegant',
            'category' => 'wedding',
            'html_path' => 'invitations.templates.wedding-elegant.index',
            'color_schemes' => [
                ['primary' => '#D4AF37', 'secondary' => '#1a1a2e', 'accent' => '#f5f0e1'],
                ['primary' => '#C9B037', 'secondary' => '#2d132c', 'accent' => '#faf0e6'],
            ],
            'is_premium' => false,
            'is_active' => true,
        ]);

        Template::create([
            'name' => 'Wedding Rustic',
            'slug' => 'wedding-rustic',
            'category' => 'wedding',
            'html_path' => 'invitations.templates.wedding-rustic.index',
            'color_schemes' => [
                ['primary' => '#8B6914', 'secondary' => '#2c1810', 'accent' => '#f4e8d1'],
                ['primary' => '#6B4423', 'secondary' => '#1a0f0a', 'accent' => '#e8d5b7'],
            ],
            'is_premium' => true,
            'is_active' => true,
        ]);

        Template::create([
            'name' => 'Birthday Fun',
            'slug' => 'birthday-fun',
            'category' => 'birthday',
            'html_path' => 'invitations.templates.birthday-fun.index',
            'color_schemes' => [
                ['primary' => '#FF6B6B', 'secondary' => '#4ECDC4', 'accent' => '#FFE66D'],
                ['primary' => '#7C4DFF', 'secondary' => '#00BCD4', 'accent' => '#FF9800'],
            ],
            'is_premium' => false,
            'is_active' => true,
        ]);

        Template::create([
            'name' => 'Wedding Minimalist',
            'slug' => 'wedding-minimalist',
            'category' => 'wedding',
            'html_path' => 'invitations.templates.wedding-minimalist.index',
            'color_schemes' => [
                ['primary' => '#000000', 'secondary' => '#fafaf9', 'accent' => '#a8a29e'],
                ['primary' => '#1a1a1a', 'secondary' => '#ffffff', 'accent' => '#9ca3af'],
            ],
            'is_premium' => true,
            'is_active' => true,
        ]);
    }
}
