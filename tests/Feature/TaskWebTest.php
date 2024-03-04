<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskWebTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_task_create_success(): void
    {
        Storage::fake();

        $user = User::factory()->create();

        $this->actingAs($user);

        $image = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post(route('tasks.store'), [
            'title' => 'Test Task',
            'description' => 'Test Task Description',
            'status' => 'pending',
            'image' => $image,
        ]);

        Storage::assertExists('tasks/' . $image->hashName());

        $response->assertRedirect( route('tasks.index') );
    }

    public function test_task_not_created_not_auth()
    {
        Storage::fake();

        $image = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post(route('tasks.store'), [
            'title' => 'Test Task',
            'description' => 'Test Task Description',
            'status' => 'pending',
            'image' => $image,
        ]);

        $response->assertRedirect( route('login') );
    }
}
