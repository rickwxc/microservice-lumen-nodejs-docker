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
      'branch_store_id' => $anotherStore->id
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

  public function testMergeBranch()
  {
    $anotherStore = factory(Store::class)->create();
    $anotherStoreBranch = factory(Store::class)->create(['parent_store_id' => $anotherStore->id]);

    $this->post('/v1/stores/'.$this->mainStore->id.'/merge', [
      'fromStoreId' => $anotherStore->id
    ], ['Accept' => 'application/json'])
      ->seeStatusCode(200)
      ->seeJson([
        'id' => $anotherStore->id,
        'parent_store_id' => $this->mainStore->id
      ])
    ;
  }
}
