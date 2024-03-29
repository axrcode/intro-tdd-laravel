<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_task_create_success(): void
    {
        Storage::fake();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $image = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson(route('api.tasks.store'), [
            'title' => 'Test Task',
            'description' => 'Test Task Description',
            'status' => 'pending',
            'image' => $image,
        ], [
            'Authorization' => 'Bearer '.$token,
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
                'user_id' => $user->id,
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
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->post(route('api.tasks.store'), [
            'title' => '',
            'description' => '',
            'status' => '',
        ], [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(422);

        $response->assertJsonStructure([ 'status', 'data', 'errors' ]);
    }

    public function test_task_not_created_not_auth()
    {
        Storage::fake();

        $image = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson(route('api.tasks.store'), [
            'title' => 'Test Task',
            'description' => 'Test Task Description',
            'status' => 'pending',
            'image' => $image,
        ]);

        $response->assertStatus(401);

        $response->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }
}
