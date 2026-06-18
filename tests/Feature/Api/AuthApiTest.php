<?php

namespace Tests\Feature\Api;

use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register_and_receive_a_sanctum_token(): void
    {
        $school = School::create(['name' => 'SMA Mentari', 'code' => 'SM']);

        $response = $this->postJson('/api/v1/auth/register', [
            'school_id' => $school->id,
            'name' => 'Nadia',
            'email' => 'nadia@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'device_name' => 'android-test',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.user.role', 'student')
            ->assertJsonStructure(['data' => ['user', 'token']]);

        $this->assertDatabaseHas('users', [
            'email' => 'nadia@example.com',
            'role' => 'student',
        ]);
    }
}
