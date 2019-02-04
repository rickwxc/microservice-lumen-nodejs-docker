<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Store;

class BranchesControllerTest extends TestCase
{
  use DatabaseMigrations;

  private $mainStore;
  private $branches;
	public function setUp()
	{
		parent::setUp();
		$this->withoutMiddleware();

    $mainStore = factory(Store::class)->create();
    $branches = factory(Store::class, 2)->create(['parent_store_id' => $mainStore->id]);
    $this->mainStore = $mainStore;
    $this->branches = $branches;
	}

	public function tearDown()
	{
		parent::tearDown();
	}

  public function testCreateBranch()
  {
    $anotherStore = factory(Store::class)->create();
    $this->post('/v1/stores/'.$this->mainStore->id.'/branches', [
      'branchStoreId' => $anotherStore->id
    ], ['Accept' => 'application/json'])
      ->seeStatusCode(200)
      ->seeJson([
        'id' => $anotherStore->id,
        'parent_store_id' => $this->mainStore->id
      ])
    ;
  }

  public function testGetStoreBranch()
  {
    $this->get('/v1/stores/'.$this->mainStore->id.'/branches', [
      'name' => ''
    ], ['Accept' => 'application/json'])
      ->seeStatusCode(200)
      ->seeJson([
        'id' => $this->branches[0]->id,
        'parent_store_id' => $this->mainStore->id
      ])
			->seeJsonStructure([
				'data' => []
			])
    ;

    $list = json_decode($this->response->getContent(), true); 
    $this->assertCount(2, $list['data']);
  }

  public function testCreateBranchFailedDueTuMainStoreIsBranchStoreChild()
  {
    $anotherStore = factory(Store::class)->create();
    $this->mainStore->parent_store_id = $anotherStore->id;
    $this->mainStore->save();

    $this->post('/v1/stores/'.$this->mainStore->id.'/branches', [
      'branchStoreId' => $anotherStore->id
    ], ['Accept' => 'application/json'])
      ->seeStatusCode(405)
      ->seeJson([
        'error' => "Main store is already branch store's child or descendants.",
      ])
    ;
  }
}
