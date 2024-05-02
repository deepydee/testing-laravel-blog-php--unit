<?php

namespace Tests\Http\Controllers;

use App\Http\Controllers\UploadController;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadControllerTest extends TestCase
{
    public function test_upload_file(): void
    {
        $this->travelTo(Carbon::make('2021-01-01 00:00:00'));

        Storage::fake('public');

        $file = UploadedFile::fake()->image('test.png');

        $this->post(action(UploadController::class), [
            'file' => $file,
        ])
        ->assertSuccessful()
        ->assertSee('/storage/uploads/2021-01-01-00-00-00-test.png');

        Storage::disk('public')->assertExists('/uploads/2021-01-01-00-00-00-test.png');
    }
}
