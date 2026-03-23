<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Package;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageCompressionUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_cover_photo_is_stored_as_webp_and_under_one_mb_for_compressible_image(): void
    {
        Storage::fake('public');
        [$user, $template, $package] = $this->bootstrapClientEntities();

        $cover = $this->makeGeneratedJpegUpload(3600, 2400, false, 'cover.jpg');

        $response = $this->actingAs($user)->post(route('client.invitations.store'), [
            'template_id' => $template->id,
            'package_id' => $package->id,
            'event_type' => 'wedding',
            'title' => 'Undangan Test Kompresi',
            'event_date' => now()->addDays(10)->format('Y-m-d'),
            'event_time' => '10:00',
            'venue_name' => 'Gedung A',
            'venue_address' => 'Jl. Mawar 1',
            'cover_photo' => $cover,
        ]);

        $response->assertRedirect();

        $invitation = Invitation::query()->latest('id')->firstOrFail();
        $this->assertNotNull($invitation->cover_photo);
        $this->assertStringEndsWith('.webp', $invitation->cover_photo);
        Storage::disk('public')->assertExists($invitation->cover_photo);

        $sizeBytes = Storage::disk('public')->size($invitation->cover_photo);
        $this->assertLessThanOrEqual(1048576, $sizeBytes, 'Compressible image should be <= 1MB.');
    }

    public function test_gallery_upload_accepts_large_input_and_saves_best_effort_webp(): void
    {
        Storage::fake('public');
        [$user, $template, $package] = $this->bootstrapClientEntities();

        $invitation = Invitation::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'package_id' => $package->id,
            'event_type' => 'wedding',
            'title' => 'Undangan Gallery Test',
            'event_date' => now()->addDays(10)->format('Y-m-d'),
            'event_time' => '10:00',
            'venue_name' => 'Gedung B',
            'venue_address' => 'Jl. Melati 2',
            'status' => 'draft',
        ]);

        $gallery = $this->makeGeneratedJpegUpload(3000, 2200, true, 'gallery.jpg');
        $originalBytes = filesize($gallery->getRealPath());
        $this->assertGreaterThan(1024 * 1024, $originalBytes);

        $response = $this->actingAs($user)->post(route('client.invitations.photos.store', $invitation), [
            'photo' => $gallery,
            'caption' => 'Foto besar',
        ]);

        $response->assertRedirect();
        $invitation->refresh();
        $photo = $invitation->photos()->latest('id')->firstOrFail();

        $this->assertStringEndsWith('.webp', $photo->file_path);
        Storage::disk('public')->assertExists($photo->file_path);

        $storedBytes = Storage::disk('public')->size($photo->file_path);
        $this->assertLessThan($originalBytes, $storedBytes, 'Best effort compression should reduce size from original upload.');
    }

    /**
     * @return array{User, Template, Package}
     */
    private function bootstrapClientEntities(): array
    {
        $user = User::factory()->create(['role' => 'client']);
        $template = Template::create([
            'name' => 'Test Template',
            'slug' => 'test-template-' . uniqid(),
            'category' => 'wedding',
            'html_path' => 'invitations.templates.wedding-gnv1.index',
            'is_active' => true,
        ]);
        $package = Package::create([
            'name' => 'Test Package',
            'slug' => 'test-package-' . uniqid(),
            'price' => 100000,
            'max_guests' => 100,
            'max_photos' => 20,
            'max_invitations' => 3,
            'features' => ['Semua Template'],
            'is_active' => true,
        ]);

        return [$user, $template, $package];
    }

    private function makeGeneratedJpegUpload(int $width, int $height, bool $noisy, string $name): UploadedFile
    {
        $tmp = tempnam(sys_get_temp_dir(), 'img_');
        if ($tmp === false) {
            throw new \RuntimeException('Unable to create temporary file.');
        }

        $image = imagecreatetruecolor($width, $height);
        if ($image === false) {
            throw new \RuntimeException('Unable to create GD image.');
        }

        for ($y = 0; $y < $height; $y++) {
            $r = (int) (40 + (($y / max(1, $height - 1)) * 160));
            $g = (int) (20 + (($y / max(1, $height - 1)) * 120));
            $b = (int) (60 + (($y / max(1, $height - 1)) * 100));
            $line = imagecolorallocate($image, min(255, $r), min(255, $g), min(255, $b));
            imageline($image, 0, $y, $width, $y, $line);
        }

        if ($noisy) {
            for ($i = 0; $i < 18000; $i++) {
                $color = imagecolorallocate(
                    $image,
                    random_int(0, 255),
                    random_int(0, 255),
                    random_int(0, 255)
                );
                imagefilledellipse(
                    $image,
                    random_int(0, $width - 1),
                    random_int(0, $height - 1),
                    random_int(3, 25),
                    random_int(3, 25),
                    $color
                );
            }
        }

        imagejpeg($image, $tmp, 100);
        imagedestroy($image);

        return new UploadedFile($tmp, $name, 'image/jpeg', null, true);
    }
}
