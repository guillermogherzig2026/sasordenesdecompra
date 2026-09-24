<?php

namespace Tests\Feature;

use App\Models\EmployeeBiometricMedia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployeeBiometricMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_upload_and_view_private_employee_media(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);

        $response = $this->actingAs($user)->postJson(route('human-resources.biometric-media.store'), [
            'employee_number' => 'EMP-001',
            'files' => [UploadedFile::fake()->image('rostro.jpg')],
        ]);

        $response->assertCreated()->assertJsonCount(1, 'items');
        $media = EmployeeBiometricMedia::query()->sole();
        $this->assertSame($user->id, $media->user_id);
        $this->assertSame('EMP-001', $media->employee_number);
        $this->assertSame('photo', $media->media_type);
        $this->assertStringEndsWith('.enc', $media->path);
        Storage::disk('local')->assertExists($media->path);
        $this->assertNotSame("\xff\xd8", substr(Storage::disk('local')->get($media->path), 0, 2));

        $this->getJson(route('human-resources.biometric-media.index'))
            ->assertOk()
            ->assertJsonPath('items.0.name', 'rostro.jpg')
            ->assertJsonPath('items.0.employeeNumber', 'EMP-001');
        $this->get(route('human-resources.biometric-media.show', $media))
            ->assertOk()
            ->assertHeader('Cache-Control', 'max-age=0, no-store, private')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_media_is_accessible_only_to_its_superadmin_owner_and_can_be_removed(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $other = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $this->actingAs($owner)->postJson(route('human-resources.biometric-media.store'), [
            'employee_number' => 'EMP-001',
            'files' => [UploadedFile::fake()->image('rostro.png')],
        ])->assertCreated();
        $media = EmployeeBiometricMedia::query()->sole();

        $this->actingAs($other)->getJson(route('human-resources.biometric-media.index'))
            ->assertOk()->assertJsonCount(0, 'items');
        $this->get(route('human-resources.biometric-media.show', $media))->assertNotFound();
        $this->deleteJson(route('human-resources.biometric-media.destroy', $media))->assertNotFound();

        $this->actingAs($owner)->deleteJson(route('human-resources.biometric-media.destroy', $media))
            ->assertOk();
        $this->assertDatabaseMissing('employee_biometric_media', ['id' => $media->id]);
        Storage::disk('local')->assertMissing($media->path);
    }

    public function test_upload_rejects_unsupported_files_and_excessive_counts(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);

        $this->actingAs($user)->postJson(route('human-resources.biometric-media.store'), [
            'employee_number' => 'EMP-001',
            'files' => [UploadedFile::fake()->create('texto.txt', 1, 'text/plain')],
        ])->assertUnprocessable()->assertJsonValidationErrors('files.0');

        $this->postJson(route('human-resources.biometric-media.store'), [
            'employee_number' => 'EMP-001',
            'files' => array_map(fn (int $index) => UploadedFile::fake()->image("foto{$index}.jpg"), range(1, 6)),
        ])->assertUnprocessable()->assertJsonValidationErrors('files');

        $this->postJson(route('human-resources.biometric-media.store'), [
            'employee_number' => 'EMP-001',
            'files' => [UploadedFile::fake()->create('video.mp4', 25601, 'video/mp4')],
        ])->assertUnprocessable()->assertJsonValidationErrors('files.0');

        $this->assertDatabaseCount('employee_biometric_media', 0);
    }

    public function test_non_superadmin_cannot_access_biometric_media(): void
    {
        $user = User::factory()->create(['role' => 'buyer', 'active' => true]);

        $this->actingAs($user)->getJson(route('human-resources.biometric-media.index'))
            ->assertForbidden();
        $this->postJson(route('human-resources.biometric-media.store'), [
            'employee_number' => 'EMP-001',
            'files' => [UploadedFile::fake()->image('rostro.jpg')],
        ])->assertForbidden();
    }

    public function test_video_upload_is_supported_and_employee_file_limit_is_enforced(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);

        $this->actingAs($user)->postJson(route('human-resources.biometric-media.store'), [
            'employee_number' => 'EMP-001',
            'files' => [UploadedFile::fake()->create('clip.mp4', 100, 'video/mp4')],
        ])->assertCreated()->assertJsonPath('items.0.type', 'video');

        foreach ([range(1, 5), range(6, 9)] as $batch) {
            $this->postJson(route('human-resources.biometric-media.store'), [
                'employee_number' => 'EMP-001',
                'files' => array_map(fn (int $index) => UploadedFile::fake()->image("foto{$index}.jpg"), $batch),
            ])->assertCreated();
        }

        $this->postJson(route('human-resources.biometric-media.store'), [
            'employee_number' => 'EMP-001',
            'files' => [UploadedFile::fake()->image('extra.jpg')],
        ])->assertUnprocessable()->assertJsonPath('message', 'Se permiten hasta 10 archivos por empleado.');
        $this->assertDatabaseCount('employee_biometric_media', 10);
    }
}
