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
				'name' => $name,
        'parent_store_id' => 0
      ])
			->seeInDatabase('stores', ['name' => $name, 'parent_store_id' => 0])
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
    $rootStores = factory(Store::class, 4)->create();
    $children = factory(Store::class, 2)->create(['parent_store_id' => $rootStores[0]->id]);
    $grandChildren = factory(Store::class, 2)->create(['parent_store_id' => $children[0]->id]);

    $this->get('/v1/stores')
      ->seeStatusCode(200)
      ->seeJsonStructure([
        'data' => []
      ])
      ;

    $list = json_decode($this->response->getContent(), true); 
    $this->assertCount(4, $list['data']);
    $this->assertArrayEqual($rootStores, $list['data'], 'id');
    $this->assertArrayEqual($list['data'][0]['branches']['data'], array_merge($children->toArray(), $grandChildren->toArray()), 'id');
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

  public function testGetOneStoreWithChildren()
  {
    $store = factory(Store::class)->create();
    $children = factory(Store::class, 2)->create(['parent_store_id' => $store->id]);

    $this->get('/v1/stores/'.$store->id.'?include=children')
      ->seeStatusCode(200)
      ->seeJsonStructure([
        'data' => []
      ])
      ->seeJson([
        'id' => $store->id
      ]);
    $store_from_api = json_decode($this->response->getContent(), true); 
    $this->assertArrayEqual([$store_from_api['data']], [$store], 'id');
    $this->assertArrayEqual($store_from_api['data']['branches']['data'], $children, 'id');
  }

  public function testGetOneStoreWithDescendants()
  {
    $store = factory(Store::class)->create();
    $child = factory(Store::class)->create(['parent_store_id' => $store->id]);
    $grandchild = factory(Store::class)->create(['parent_store_id' => $child->id]);

    $this->get('/v1/stores/'.$store->id.'?include=descendant')
      ->seeStatusCode(200)
      ->seeJsonStructure([
        'data' => []
      ])
      ->seeJson([
        'id' => $store->id
      ])
      ;

    $store_from_api = json_decode($this->response->getContent(), true); 
    $this->assertArrayEqual([$store_from_api['data']], [$store], 'id');
    $this->assertArrayEqual($store_from_api['data']['branches']['data'], [$child, $grandchild], 'id');
  }

  public function testGetOneStoreFailedDueToIdNotFound()
  {
    $this->get('/v1/stores/999')->seeStatusCode(404);
    $this->get('/v1/stores/notexistid')->seeStatusCode(404);
  }
}
