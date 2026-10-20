<?php

namespace Tests\Feature;

use App\Models\Internship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InternshipApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_access_to_internships_returns_401(): void
    {
        $response = $this->getJson('/api/internships');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_perform_full_crud_on_internships(): void
    {
        Sanctum::actingAs(User::factory()->create());

        // 1. Create (201 Created)
        $createResponse = $this->postJson('/api/internships', [
            'student_name' => 'Ahmad Fahim',
            'company_name' => 'PT Telkom Indonesia',
            'position' => 'DevOps Intern',
            'start_date' => '2026-10-20',
            'status' => 'Diterima',
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.student_name', 'Ahmad Fahim');

        $id = $createResponse->json('data.id');

        // 2. Get All (200 OK)
        $this->getJson('/api/internships')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // 3. Get One (200 OK)
        $this->getJson("/api/internships/{$id}")
            ->assertStatus(200)
            ->assertJsonPath('data.company_name', 'PT Telkom Indonesia');

        // 4. Update (200 OK)
        $this->putJson("/api/internships/{$id}", [
            'status' => 'Selesai',
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'Selesai');

        // 5. Delete (200 OK)
        $this->deleteJson("/api/internships/{$id}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('internships', ['id' => $id]);
    }

    public function test_validation_error_on_create_internship_returns_422(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/internships', [
            'student_name' => '',
            'company_name' => '',
            'position' => '',
            'start_date' => 'invalid-date',
            'status' => 'InvalidStatus',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['student_name', 'company_name', 'position', 'start_date', 'status']);
    }
}
