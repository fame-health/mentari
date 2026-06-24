<?php

namespace Tests\Feature\Api;

use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_schools_can_be_listed_before_login(): void
    {
        School::create(['name' => 'SMA Mentari B', 'code' => 'SMB']);
        School::create(['name' => 'SMA Mentari A', 'code' => 'SMA']);

        $response = $this->getJson('/api/v1/schools');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.name', 'SMA Mentari A')
            ->assertJsonPath('schools.0.name', 'SMA Mentari A')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'code', 'address'],
                ],
                'schools' => [
                    '*' => ['id', 'name', 'code', 'address'],
                ],
            ]);
    }

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
            ->assertJsonPath('data.user.school_id', $school->id)
            ->assertJsonStructure(['data' => ['user', 'token']]);

        $this->assertDatabaseHas('users', [
            'email' => 'nadia@example.com',
            'role' => 'student',
            'school_id' => $school->id,
        ]);
    }

    public function test_student_registration_requires_a_registered_school(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Nadia',
            'email' => 'nadia@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'device_name' => 'android-test',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('school_id');
    }
}
