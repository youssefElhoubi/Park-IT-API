<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    // this test is to check if the sign up function is working
    public function test_success_SignUp()
    {
        $response = $this->postJson("/api/signup", [
            "name" => "jamal",
            "email" => "jamal@gmail.com",
            "password" => "jamal123",
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(["token"]);
    }
    // this one check of the email alredy exists in the db
    public function test_email_exists_inDb()
    {
        User::factory()->create([
            "name" => "jamal",
            "email" => "jamal@gmail.com",
            "password" => bcrypt("12341234"),
            "role"=>"admine"
        ]);
        $responce = $this->postJson("api/signup", [
            "name" => "jamal",
            "email" => "jamal@gmail.com",
            "password" => "12341234"
        ]);
        $responce->assertStatus(Response::HTTP_BAD_REQUEST)->assertJson(["message" => 'Email already exists']);
    }
    // this one checks if all fealds are filled 
    public function test_if_any_feald_is_missing()
    {
        $responce = $this->postJson("api/signup", []);
        $responce->assertStatus(Response::HTTP_BAD_REQUEST)->assertJson([
            'message' => 'The email field is required. (and 2 more errors)',
            'details' => [
                'email' =>
                    ['The email field is required.'],
                'password' =>
                    ['The password field is required.'],
                'name' =>
                    ['The name field is required.'],
            ]
        ]);
    }
    public function test_if_password_is_too_short()
    {
        $responce = $this->postJson("api/signup", [
            "name" => "jamal",
            "email" => "jamal@gmail.com",
            "password" => "123"
        ]);
        $responce->assertStatus(Response::HTTP_BAD_REQUEST)->assertJson([
            'message' => 'The password field must be at least 6 characters.',
            'details' => [
                'password' =>
                    ['The password field must be at least 6 characters.'],
            ]
        ]);
    }
    public function test_if_the_email_is_not_correct()
    {
        $responce = $this->postJson("api/signup", [
            "name" => "jamal",
            "email" => "jamalgmail.com",
            "password" => "12341234"
        ]);
        $responce->assertStatus(Response::HTTP_BAD_REQUEST)->assertJson([
            'message' => 'The email field must be a valid email address.',
            'details' => [
                'email' =>
                    ['The email field must be a valid email address.'],
            ]
        ]);
    }
    // log in tests 
    public function test_User_log_inSucssecc_fully()
    {
        $user = User::factory()->create([
            "name" => "jamal",
            "email" => "jamal@gmail.com",
            "password" => bcrypt("12341234"),
            "role"=>"admine"
        ]);
        $Reponse = $this->postJson("api/signin", [
            "email" => "jamal@gmail.com",
            "password" => "12341234"
        ]);
        $Reponse->assertStatus(Response::HTTP_OK)->assertJsonStructure([
            "token"
        ]);
    }
    public function test_User_does_not_exist()
    {

        $Reponse = $this->postJson("api/signin", [
            "email" => "jamal@gmail.com",
            "password" => "12341234"
        ]);
        $Reponse->assertStatus(Response::HTTP_BAD_REQUEST)->assertJsonStructure([
            "message"
        ]);
    }
    public function test_User_if_user_password_is_not_correct()
    {
        $user = User::factory()->create([
            "name" => "jamal",
            "email" => "jamal@gmail.com",
            "password" => bcrypt("12341234"),
            "role"=>"admine"
        ]);
        $Reponse = $this->postJson("api/signin", [
            "email" => "jamal@gmail.com",
            "password" => "12341235"
        ]);
        $Reponse->assertStatus(Response::HTTP_BAD_REQUEST)->assertJsonStructure([
            "message"
        ]);
    }
    public function test_if_any_feald_is_missing_login()
    {
        $responce = $this->postJson("api/signin", []);
        $responce->assertStatus(Response::HTTP_BAD_REQUEST)->assertJsonStructure(["message","details"]);
    }

}
