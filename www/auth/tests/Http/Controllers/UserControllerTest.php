<?php

namespace Tests\App\Http\Controllers;

use TestCase;
use Carbon\Carbon;
use App\Transformer\UserTransformer;
use Laravel\Lumen\Testing\DatabaseMigrations;


class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::now('UTC'));
    }

    public function tearDown()
    {
        parent::tearDown();

        Carbon::setTestNow();
    }

    /** @test **/
    public function can_register_user()
    {
			$email = 'alice@testgmail.com';

			$this->post('/v1/users/register', [
				'name' => 'alice',
				'email' => $email,
				'password' => 'abcd8910',
      ], ['Accept' => 'application/json'])
      ->seeStatusCode(201)
      ->seeHeaderWithRegExp('Location', '#/users/[\d]+$#')
			->seeJson([
				'name' => 'alice',
				'email' => $email,
      ])
			->seeInDatabase('users', ['email' => $email])
      ;
    }

    /** @test **/
    public function reject_register_user()
    {
			$email = 'alice@testgmail.com';

			$this->post('/v1/users/register', [
				'email' => $email,
				'password' => 'abcd8910',
      ], ['Accept' => 'application/json'])
      ->seeStatusCode(422)
      ->notSeeInDatabase('users', ['email' => $email])
      ;
      $body = $this->response->getContent(); 

      $this->assertRegExp('/error/i', $body);
    }

    /** @test **/
    public function welcome()
    {

      $this->get('/v1/users/welcome'); 
      $body = $this->response->getContent(); 
      $this->assertEquals($body, __('messages.welcome'));
    }

    /** @test **/
    public function test()
    {
      $user = factory('App\User', 2)->create();

      $this->get('/v1/users/test');
      $expected = [
        'data' => $user->toArray()
      ];
      $body = json_decode($this->response->getContent(), true); 

      $this->assertArrayHasKey('data', $body);

      $user = factory('App\User')->create();
      $subject = new UserTransformer();
      $transform = $subject->transform($user);
      $this->assertArrayHasKey('id', $transform);
      $this->assertArrayHasKey('name', $transform);
      $this->assertArrayHasKey('created', $transform);
      $this->assertArrayHasKey('updated', $transform);
    }
}
