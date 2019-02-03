<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Store;

class StoresControllerTest extends TestCase
{
  use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();
		$this->withoutMiddleware();
	}

	public function tearDown()
	{
		parent::tearDown();
	}

  public function testCreateStore()
  {
			$name = 'alice store';
      $this->notSeeInDatabase('stores', ['name' => $name]);

			$this->post('/v1/stores', [
        'name' => $name
      ], ['Accept' => 'application/json'])
      ->seeStatusCode(201)
      ->seeHeaderWithRegExp('Location', '#/stores/[\d]+$#')
			->seeJson([
				'name' => $name
      ])
			->seeInDatabase('stores', ['name' => $name])
      ;
  }

  public function testCreateStoreFaildDueToMissingName()
  {
			$this->post('/v1/stores', [
				'name' => ''
      ], ['Accept' => 'application/json'])
      ->seeStatusCode(422)
			->notSeeInDatabase('stores', ['name' => ''])
      ;
  }

  public function testUpdateStore()
  {
		$old_name = 'alice store';
		$new_name = 'Bob store';
		$store = factory(Store::class)->create(['name' => $old_name]);
		$this->seeInDatabase('stores', ['name' => $old_name]);
		$this->notSeeInDatabase('stores', ['name' => $new_name]);

		$this->put('/v1/stores/'.$store->id, [
			'name' => $new_name
		], ['Accept' => 'application/json'])
		->seeStatusCode(200)
		->seeJson([
			'name' => $new_name
		])
		->seeInDatabase('stores', ['name' => $new_name])
		->notSeeInDatabase('stores', ['name' => $old_name])
		;
  }

  public function testUpdateStoreFailedWithEmptyName()
  {
		$old_name = 'alice store';
		$store = factory(Store::class)->create(['name' => $old_name]);
		$this->seeInDatabase('stores', ['name' => $old_name])
			->notSeeInDatabase('stores', ['name' => '']);

		$this->put('/v1/stores/'.$store->id, [
			'name' => ''
		], ['Accept' => 'application/json'])
		->seeStatusCode(422)
		->notSeeInDatabase('stores', ['name' => ''])
		;
		$this->seeInDatabase('stores', ['name' => $old_name]);
  }

  public function testGetAllStores()
  {
    factory(Store::class, 4)->create();

    $this->assertCount(4, Store::all());

    $this->get('/v1/stores')
      ->seeStatusCode(200)
			->seeJsonStructure([
				'data' => []
			])
      ;

    $list = json_decode($this->response->getContent(), true); 
    $this->assertCount(4, $list['data']);
  }

  public function testDeleteStore()
  {
    $store = factory(Store::class)->create();
    $this->get('/v1/stores/'.$store->id)->seeStatusCode(200);

    $this->delete('/v1/stores/'.$store->id)
      ->seeStatusCode(204)
      ->isEmpty()
      ;
    $this->get('/v1/stores/'.$store->id)->seeStatusCode(404);
  }

  public function testGetOneStore()
  {
    $store = factory(Store::class)->create();

    $this->get('/v1/stores/'.$store->id)
      ->seeStatusCode(200)
      ->seeJsonStructure([
        'data' => []
      ])
      ->seeJson([
        'id' => $store->id
      ]);
  }

  public function testGetOneStoreFailedDueToIdNotFound()
  {
    $this->get('/v1/stores/999')->seeStatusCode(404);
    $this->get('/v1/stores/notexistid')->seeStatusCode(404);
  }
}
