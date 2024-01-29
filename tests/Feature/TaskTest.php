<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_task_create_success(): void
    {
        Storage::fake();

        $image = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post(route('tasks.store'), [
            'title' => 'Test Task',
            'description' => 'Test Task Description',
            'status' => 'pending',
            'image' => $image,
        ]);

        $response->assertStatus(200);

        Storage::assertExists('tasks/' . $image->hashName());

        $response->assertJson([
            'status' => 'success',
            'data' => [
                'title' => 'Test Task',
                'description' => 'Test Task Description',
                'status' => 'pending',
                'image' => 'tasks/' . $image->hashName(),
            ]
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test Task Description',
            'status' => 'pending',
            'image' => 'tasks/' . $image->hashName(),
        ]);
    }

    public function test_no_validation_passed()
    {
        $response = $this->post(route('tasks.store'), [
            'title' => '',
            'description' => '',
            'status' => '',
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(422);

        $response->assertJsonStructure([ 'status', 'data', 'errors' ]);
    }
}
