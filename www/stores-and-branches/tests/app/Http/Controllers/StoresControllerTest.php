<?php
// todo IfHaveTime: add create/update protected fields check

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Store;

class StoresTest extends TestCase
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
				'status' => Store::ACTIVE
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

  public function testPutStore()
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
			'name' => $new_name,
			'status' => Store::ACTIVE
		])
		->seeInDatabase('stores', ['name' => $new_name])
		->notSeeInDatabase('stores', ['name' => $old_name])
		;
  }

  public function testPutStoreFailedWithEmptyName()
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
  }

  public function testGetAllStores()
  {
    $stores = factory(Store::class, 4)->create();
    $stores = factory(Store::class, 2)->create(['status' => Store::PENDING_DELETE]);

    $this->get('/v1/stores')
      ->seeStatusCode(200)
      ->seeJson([
        'status' => Store::ACTIVE
      ])
			->seeJsonStructure([
				'data' => []
			])
      ->dontSeeJson([
        'status' => Store::PENDING_DELETE
      ])
      ;

    $list = json_decode($this->response->getContent(), true); 
    $this->assertCount(4, $list['data']);
  }

  public function testDeleteStoreSuccess()
  {
    $storeId = 123;
    $stores = factory(Store::class, 1)->create(['id' => $storeId]);
    $this->get('/v1/stores/'.$storeId)->seeStatusCode(200);

    $this->delete('/v1/stores/'.$storeId)
      ->seeStatusCode(204)
      ->isEmpty()
      ;
    $this->get('/v1/stores/'.$storeId)->seeStatusCode(404);
  }

  public function testGetOneStoreSuccess()
  {
    $storeId = 123;
    $stores = factory(Store::class, 1)->create(['id' => $storeId]);

    $this->get('/v1/stores/'.$storeId)
      ->seeStatusCode(200)
			->seeJsonStructure([
				'data' => []
			])
      ->seeJson([
        'id' => $storeId,
        'status' => Store::ACTIVE
      ]);
  }

  public function testGetOneStoreFailedDueToIdNotFound()
  {
    $this->get('/v1/stores/999')->seeStatusCode(404);
    $this->get('/v1/stores/notexistid')->seeStatusCode(404);
  }

  public function testGetOneStoreFailedDueToPendingDelete()
  {
    $storeId = 123;
    $stores = factory(Store::class, 1)->create(['id' => $storeId]);
    $this->get('/v1/stores/'.$storeId)->seeStatusCode(200);

    $store = Store::findOrFail($storeId);
    $store->status = Store::PENDING_DELETE;
    $store->save();
    $this->get('/v1/stores/'.$storeId)->seeStatusCode(404);
  }
}
