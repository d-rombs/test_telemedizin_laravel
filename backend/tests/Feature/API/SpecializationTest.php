<?php

namespace Tests\Feature\API;

use App\Models\Specialization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpecializationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test retrieving all specializations.
     */
    public function test_can_get_all_specializations(): void
    {
        // Create test specializations
        Specialization::create(['name' => 'Test Specialization 1']);
        Specialization::create(['name' => 'Test Specialization 2']);

        // Make request to API
        $response = $this->getJson('/api/specializations');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => 'Test Specialization 1'])
            ->assertJsonFragment(['name' => 'Test Specialization 2']);
    }

    /**
     * Test creating a new specialization.
     */
    public function test_can_create_specialization(): void
    {
        $data = ['name' => 'New Specialization'];

        $response = $this->postJson('/api/specializations', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('specializations', $data);
    }

    /**
     * Test retrieving a single specialization.
     */
    public function test_can_get_single_specialization(): void
    {
        $specialization = Specialization::create(['name' => 'Test Specialization']);

        $response = $this->getJson('/api/specializations/' . $specialization->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Test Specialization']);
    }

    /**
     * Test updating a specialization.
     */
    public function test_can_update_specialization(): void
    {
        $specialization = Specialization::create(['name' => 'Test Specialization']);

        $data = ['name' => 'Updated Specialization'];

        $response = $this->putJson('/api/specializations/' . $specialization->id, $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('specializations', $data);
    }

    /**
     * Test deleting a specialization.
     */
    public function test_can_delete_specialization(): void
    {
        $specialization = Specialization::create(['name' => 'Test Specialization']);

        $response = $this->deleteJson('/api/specializations/' . $specialization->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('specializations', ['id' => $specialization->id]);
    }
}
