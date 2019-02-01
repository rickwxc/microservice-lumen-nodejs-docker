<?php

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
