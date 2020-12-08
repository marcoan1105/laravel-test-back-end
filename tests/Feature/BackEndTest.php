<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackEndTest extends TestCase
{
    protected $name = "Unit Test";
    protected $email = "test@test.com.br";
    protected $password = "password";
    protected $accessToken = "Bearer ";
    protected $headers = [];

    /**
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadUserTest();
    }

    /**
     * Test crete user
     *
     * @return void
     */
    public function testCreateUser(){
        $response = $this->post('/api/auth/register', [
            "name" => "Unit Test",
            "email" => "unittest@test.com.br",
            "password" => "password",
        ]);

        $response->assertJson([
            "status" => true
        ]);
    }

    /**
     * Test login user
     *
     * @return void
     */
    public function testLoginUser(){
        $response = $this->post('/api/auth/login', [
            "email" => "unittest@test.com.br",
            "password" => "password",
        ]);

        $response->assertJsonStructure([
            "access_token", "token_type", "expires_in"
        ]);
    }

    /**
     * Test color
     *
     * @return void
     */

    public function testColor()
    {
        try{

            // create color
            $responseCreate = $this->post('/api/color', [
                "description" => "Branco e Preto",
            ], $this->headers);

            $responseCreate->assertJson([
                "status" => true
            ]);

            // update color
            $response = $this->put('/api/color/'.$responseCreate->original["data"]["id"], [
                "description" => "Branco Escuro",
            ], $this->headers);

            $response->assertJson([
                "status" => true
            ]);

            // get all colors
            $responseAllColors = $this->get('/api/colors', $this->headers);

            $responseAllColors->assertJson([
                "status" => true
            ]);

            $this->assertTrue(count($responseAllColors->original["data"])  > 0);

            //delete color
            $response = $this->delete('/api/color/'.$responseCreate->original["data"]["id"], [
            ], $this->headers);

            $response->assertJson([
                "status" => true
            ]);

        }catch (PHPUnit_Framework_AssertionFailedError $e){
            $this->fail($e->getMessage());
        }
    }

    /**
     * Test Product
     * @return void
     */
    public function testProduct()
    {
        try{

            // create color
            $responseColor = $this->post('/api/color', [
                "description" => "Branco e Preto",
            ], $this->headers);

            $responseColor->assertJson([
                "status" => true
            ]);

            // create product
            $responseCreateProduct = $this->post('/api/product', [
                "description" => "Produto",
                "price" => 1,
                "colors" => [$responseColor->original["data"]["id"]]
            ], $this->headers);

            $responseCreateProduct->assertJson([
                "status" => true
            ]);


            // update product
            $responseUpdateProduct = $this->put('/api/product/' . $responseCreateProduct->original["data"]["id"], [
                "description" => "Produto 2",
                "price" => 1,
                "colors" => [$responseColor->original["data"]["id"]]
            ], $this->headers);

            $responseUpdateProduct->assertJson([
                "status" => true,
                "data" => [
                    "description" => "Produto 2"
                ]
            ]);

            // get all products
            $responseAllProducts = $this->get('/api/products', $this->headers);

            $responseAllProducts->assertJson([
                "status" => true
            ]);

            $this->assertTrue(count($responseAllProducts->original["data"])  > 0);

            // get product
            $responseProduct = $this->get('/api/product/'.$responseCreateProduct->original["data"]["id"], $this->headers);

            $responseProduct->assertJsonStructure([
                "status",
                "data" => [
                    "id", "description", "price", "colors"
                ]
            ]);

            $responseProduct->assertJson([
                "status" => true,
                "data" => [
                    "description" => "Produto 2"
                ]
            ]);

            // delete product
            $responseDeleteProduct = $this->delete('/api/product/'.$responseCreateProduct->original["data"]["id"], [
            ], $this->headers);

            $responseDeleteProduct->assertJson([
                "status" => true
            ]);

            //delete color
            $response = $this->delete('/api/color/'.$responseColor->original["data"]["id"], [
            ], $this->headers);

            $response->assertJson([
                "status" => true
            ]);

        }catch (PHPUnit_Framework_AssertionFailedError $e){
            $this->fail($e->getMessage());
        }
    }

    /**
     * Test delete users
     *
     * @return void
     */
    public function testDeleteUsers(){
        $response = $this->delete('/api/user/email', [
            "email" => $this->email
        ], $this->headers);

        $response->assertJson([
            "status" => true
        ]);

        $response = $this->delete('/api/user/email', [
            "email" => "unittest@test.com.br"
        ], $this->headers);

        $response->assertJson([
            "status" => true
        ]);
    }

    /**
     * Load users to test
     *
     * @return void
     */
    public function loadUserTest(){
        $response = $this->post('/api/auth/register', [
            "name" => $this->name,
            "email" => $this->email,
            "password" => $this->password,
        ]);

        $response = $this->post('/api/auth/login', [
            "email" => $this->email,
            "password" => $this->password,
        ]);

        if(isset($response->original["access_token"])){
            $this->accessToken = "Bearer ".$response->original["access_token"];
            $this->headers = [
                "Authorization" => $this->accessToken
            ];
        }
    }
}
