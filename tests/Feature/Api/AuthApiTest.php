<?php

namespace Tests\Feature\Api;

use App\Models\Classroom;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_schools_can_be_listed_before_login(): void
    {
        School::create(['name' => 'SMA Mentari B', 'code' => 'SMB']);
        $school = School::create(['name' => 'SMA Mentari A', 'code' => 'SMA']);
        Classroom::create([
            'school_id' => $school->id,
            'name' => 'X IPA 1',
            'sort_order' => 1,
        ]);

        $response = $this->getJson('/api/v1/schools');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.name', 'SMA Mentari A')
            ->assertJsonPath('data.0.classrooms.0.name', 'X IPA 1')
            ->assertJsonPath('schools.0.name', 'SMA Mentari A')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'code', 'address', 'classrooms'],
                ],
                'schools' => [
                    '*' => ['id', 'name', 'code', 'address', 'classrooms'],
                ],
            ]);
    }

    public function test_student_can_register_and_receive_a_sanctum_token(): void
    {
        $school = School::create(['name' => 'SMA Mentari', 'code' => 'SM']);
        $classroom = Classroom::create([
            'school_id' => $school->id,
            'name' => 'X',
            'sort_order' => 1,
        ]);

        $response = $this->postJson('/api/v1/auth/register', [
            'school_id' => $school->id,
            'classroom_id' => $classroom->id,
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
            ->assertJsonPath('data.user.classroom_id', $classroom->id)
            ->assertJsonPath('data.user.level', 'X')
            ->assertJsonStructure(['data' => ['user', 'token']]);

        $this->assertDatabaseHas('users', [
            'email' => 'nadia@example.com',
            'role' => 'student',
            'school_id' => $school->id,
            'classroom_id' => $classroom->id,
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

    public function test_student_can_update_classroom_from_android(): void
    {
        $oldSchool = School::create(['name' => 'SMA Mentari Lama', 'code' => 'SML']);
        $oldClassroom = Classroom::create([
            'school_id' => $oldSchool->id,
            'name' => 'X',
            'sort_order' => 1,
        ]);
        $newSchool = School::create(['name' => 'SMA Mentari Baru', 'code' => 'SMB']);
        $newClassroom = Classroom::create([
            'school_id' => $newSchool->id,
            'name' => 'XI IPA 1',
            'sort_order' => 1,
        ]);
        $user = User::factory()->create([
            'school_id' => $oldSchool->id,
            'classroom_id' => $oldClassroom->id,
            'level' => 'X',
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/v1/auth/classroom', [
            'classroom_id' => $newClassroom->id,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Kelas berhasil diperbarui.')
            ->assertJsonPath('data.user.school_id', $newSchool->id)
            ->assertJsonPath('data.user.classroom_id', $newClassroom->id)
            ->assertJsonPath('data.user.level', 'XI IPA 1')
            ->assertJsonPath('data.user.classroom.name', 'XI IPA 1')
            ->assertJsonPath('data.user.school.name', 'SMA Mentari Baru');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'school_id' => $newSchool->id,
            'classroom_id' => $newClassroom->id,
            'level' => 'XI IPA 1',
        ]);
    }

    public function test_student_cannot_update_to_inactive_classroom(): void
    {
        $school = School::create(['name' => 'SMA Mentari', 'code' => 'SM']);
        $activeClassroom = Classroom::create([
            'school_id' => $school->id,
            'name' => 'X',
            'sort_order' => 1,
        ]);
        $inactiveClassroom = Classroom::create([
            'school_id' => $school->id,
            'name' => 'XI',
            'sort_order' => 2,
            'is_active' => false,
        ]);
        $user = User::factory()->create([
            'school_id' => $school->id,
            'classroom_id' => $activeClassroom->id,
            'level' => 'X',
        ]);

        Sanctum::actingAs($user);

        $this->patchJson('/api/v1/auth/classroom', [
            'classroom_id' => $inactiveClassroom->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('classroom_id');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'classroom_id' => $activeClassroom->id,
            'level' => 'X',
        ]);
    }
}
