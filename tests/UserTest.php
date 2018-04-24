<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;

class UserTest extends TestCase
{
    private $JWT_REGEX = '/[\'\"]token[\'\"]:[\'\"][a-zA-Z0-9\-_]+\.[a-zA-Z0-9\-_]+\.[a-zA-Z0-9\-_]+[\'\"]/';

    /**
     * As a guest, should be able to register a new user
     *
     * @return void
     */
    public function testRegisterUser()
    {        
        $requestBody = [
            'user' => [
                'username' => 'TestUser', 
                'email' => 'test_user@fakemail.com',
                'password' => 'my_pass_2345'
            ]
        ];

        // Make register request
        $this->post('/api/user/register', $requestBody)
             ->assertResponseOk();

        // Assert that token was returned
        $this->assertRegExp($this->JWT_REGEX, $this->response->getContent());
    }

    /**
     * As a guest, should be able to login an existing user
     *
     * @return void
     */
    public function testLoginUser()
    {    
        // create a test user in database
        User::create([
            'username' => 'TestUser',
            'email' => 'test_user@fakemail.com',
            'password' => bcrypt('my_pass_2345')
        ]);

        $requestBody = [
            'user' => [
                'email' => 'test_user@fakemail.com',
                'password' => 'my_pass_2345'
            ]
        ];

        // Make login request
        $this->post('/api/user/login', $requestBody)
             ->assertResponseOk();

        // Assert that token was returned
        $this->assertRegExp($this->JWT_REGEX, $this->response->getContent());
    }
}
