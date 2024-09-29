<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    /**
     * A basic feature test to test if apis are working.
     */
    public function test_api_working(): void
    {
        $response = $this->get('/api');

        $response->assertStatus(200);
    }

    public function test_create_new_user(): void
    {
        $user = User::factory()->make();
        $response = $this->postJson('/api/users', [
            'name' => $user->name,
            'age' => $user->age,
            'points' => $user->points,
            'address' => $user->address,
        ]);
        $response->assertStatus(201);

        $response->assertJson([
            'error' => false,
            'message' => 'User added successfully',
            'data' => [
                'name' => $user->name,
                'age' => $user->age,
                'points' => 0,
                'address' => $user->address
            ]
        ]);
    }

    public function test_create_existing_user(): void
    {
        $user = User::factory()->create();
        $response = $this->postJson('/api/users', [
            'name' => $user->name,
            'age' => $user->age,
            'points' => $user->points,
            'address' => $user->address,
        ]);
        $response->assertStatus(422);

        $response->assertJson([
            'error' => true,
            'message' => "Validation error occurred.",
            'data' => [
                'name' => [
                    'The name has already been taken.'
                ]
            ]
        ]);
    }

    public function test_get_users(): void
    {
        $existingUsersCount = User::count();

        User::factory()->count(10)->create();
        $response = $this->get('/api/users');

        $response->assertStatus(200);
        $totalUsers = $existingUsersCount + 10;
        $response->assertJsonCount($totalUsers, 'data');
        $response->assertJsonStructure([
            'error',
            'message',
            'data' => [
                '*' => ['id', 'name', 'age', 'points', 'address']
            ]
        ]);
    }

    public function test_reset_scores(): void
    {
        $existingUsersCount = User::count();

        User::factory()->count(5)->create(['points' => 100]);
        $response = $this->post('/api/users/scores/reset');

        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'message' => 'All user scores have been reset to 0',
            'data' => null
        ]);

        $this->assertDatabaseMissing('users', ['points' => 100]);

        $totalUsers = $existingUsersCount + 5;
        $this->assertDatabaseCount('users', $totalUsers);
        $this->assertDatabaseHas('users', ['points' => 0]);
    }

    public function test_increment_user_score(): void
    {
        $user = User::factory()->create();

        $response = $this->put("/api/user/{$user->id}/increment");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'error',
            'message',
            'data' => [
                'id', 'name', 'points'
            ]
        ]);
        $this->assertGreaterThan($user->points, $user->fresh()->points);
    }

    public function test_decrement_user_score(): void
    {
        $user = User::factory()->create();

        $response = $this->put("/api/user/{$user->id}/decrement");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'error',
            'message',
            'data' => [
                'id', 'name', 'points'
            ]
        ]);
        $this->assertLessThan($user->points, $user->fresh()->points);
    }

    public function test_delete_existing_user(): void
    {
        $user = User::factory()->create();
        $response = $this->delete("/api/user/{$user->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'error' => false,
            'message' => "User deleted successfully",
            'data' => null
        ]);

        $this->assertNull(User::find($user->id));
    }

    public function test_delete_unknown_user(): void
    {
        $nonExistentUserId = 9999999;
        $response = $this->delete("/api/user/{$nonExistentUserId}");

        $response->assertStatus(404);
        $response->assertJson([
            'error' => true,
            'message' => "User not found"
        ]);
    }

    public function test_get_winners(): void
    {
        $response = $this->get('/api/winners');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'error',
            'message',
            'data' => [
                '*' => ['id', 'user_id', 'points']
            ]
        ]);
    }
}
