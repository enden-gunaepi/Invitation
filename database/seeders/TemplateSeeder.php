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
            'thumbnail' => 'template-thumbnails/wedding-elegant.svg',
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
            'thumbnail' => 'template-thumbnails/wedding-rustic.svg',
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
            'thumbnail' => 'template-thumbnails/birthday-fun.svg',
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
            'thumbnail' => 'template-thumbnails/wedding-minimalist.svg',
            'html_path' => 'invitations.templates.wedding-minimalist.index',
            'color_schemes' => [
                ['primary' => '#000000', 'secondary' => '#fafaf9', 'accent' => '#a8a29e'],
                ['primary' => '#1a1a1a', 'secondary' => '#ffffff', 'accent' => '#9ca3af'],
            ],
            'is_premium' => true,
            'is_active' => true,
        ]);

        Template::create([
            'name' => 'Wedding Peach Garden',
            'slug' => 'wedding-peach',
            'category' => 'wedding',
            'thumbnail' => 'template-thumbnails/wedding-peach.svg',
            'html_path' => 'invitations.templates.wedding-peach.index',
            'color_schemes' => [
                ['primary' => '#E89570', 'secondary' => '#FFF7F2', 'accent' => '#D97979'],
                ['primary' => '#F6B494', 'secondary' => '#FFFFFF', 'accent' => '#9C6246'],
            ],
            'is_premium' => true,
            'is_active' => true,
        ]);

        Template::create([
            'name' => 'Wedding GNV1',
            'slug' => 'wedding-gnv1',
            'category' => 'wedding',
            'thumbnail' => null,
            'html_path' => 'invitations.templates.wedding-gnv1.index',
            'color_schemes' => [
                ['primary' => '#BE123C', 'secondary' => '#111827', 'accent' => '#FCE7F3'],
                ['primary' => '#7F1D1D', 'secondary' => '#0F172A', 'accent' => '#FFE4E6'],
            ],
            'is_premium' => false,
            'is_active' => true,
        ]);

        Template::create([
            'name' => 'Wedding GNV2',
            'slug' => 'wedding-gnv2',
            'category' => 'wedding',
            'thumbnail' => null,
            'html_path' => 'invitations.templates.wedding-gnv2.index',
            'color_schemes' => [
                ['primary' => '#9fbfd6', 'secondary' => '#000000', 'accent' => '#e2f1ff'],
                ['primary' => '#89adc6', 'secondary' => '#111827', 'accent' => '#f4faff'],
            ],
            'is_premium' => false,
            'is_active' => true,
        ]);
    }
}
