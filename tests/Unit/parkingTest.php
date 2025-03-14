<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\parking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

class parkingTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */ 
    public function test_create_parking()
    {
        $user =User::factory()->create([
            "name" => "jamal",
            "email" => "jamal@gmail.com",
            "password" => bcrypt("12341234"),
            "role"=>"admine"
        ]);
        Sanctum::actingAs($user);
        $response = $this->postJson("api/parking/create", [
            'name' => "somekinde of info",
            'totale_spost' => "somekinde of info",
            "availabel_spots" => "somekinde of info",
        ]);
        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure(["message","marpking"]);
    }

}
