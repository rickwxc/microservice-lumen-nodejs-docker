<?php

namespace Tests\App\Http\Controllers;

use TestCase;
use Carbon\Carbon;
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
				'password' => 'abcd890',
			], ['Accept' => 'application/json'])
			->seeStatusCode(201)
			->seeJson([
				'name' => 'alice',
				'email' => $email,
			])
			;

			$body = json_decode($this->response->getContent(), true);
			$this->seeInDatabase('users', ['email' => $email]);
    }
}
