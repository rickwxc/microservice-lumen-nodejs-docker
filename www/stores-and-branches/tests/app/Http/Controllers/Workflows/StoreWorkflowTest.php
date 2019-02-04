<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Store;
use App\Http\Controllers\Workflows\StoreWorkflow;
use App\Http\Exceptions\WorkflowException;

class StoreWorkflowTest extends TestCase
{
  use DatabaseMigrations;

  private $mainStore;
  private $anotherStore;
  private $anotherStoreBranches;

  private $storeWorkflow;
	public function setUp()
	{
		parent::setUp();
    $this->storeWorkflow = new StoreWorkflow();

    $mainStore = factory(Store::class)->create();
    $branches = factory(Store::class, 2)->create(['parent_store_id' => $mainStore->id]);
    $this->mainStore = $mainStore;

	}

  private function setupLeveledStoreBranches(){
    $l1 = factory(Store::class)->create(['parent_store_id' => $this->mainStore->id]);
    $l2 = factory(Store::class)->create(['parent_store_id' => $l1->id]);
    $l3 = factory(Store::class)->create(['parent_store_id' => $l2->id]);
    $l4 = factory(Store::class)->create(['parent_store_id' => $l3->id]);
  }

	public function tearDown()
	{
		parent::tearDown();
	}

  public function testDelete()
  {
    $this->setupLeveledStoreBranches();
    $this->storeWorkflow->processDelete($this->mainStore->id);

    $this->assertEmpty(Store::all()->toArray());
  }

  public function testMergeBranchFailedDueToSameStoreId()
  {
    $this->expectException(WorkflowException::class);
    $this->expectExceptionCode(412);
    $this->expectExceptionMessage('Can not merge same store.');
    
    $fromStoreId = $targetStoreId = $this->mainStore->id;
    $this->storeWorkflow->mergeBranch($fromStoreId, $targetStoreId);
  }

  public function testMergeBranchFailedDueToFromStoreIdNotFound()
  {
    $this->expectException(WorkflowException::class);
    $this->expectExceptionCode(404);
    $this->storeWorkflow->addBranch('invalid from store id', $this->mainStore->id);
  }

  public function testMergeBranchFailedDueToTargetStoreIdNotFound()
  {
    $this->expectException(WorkflowException::class);
    $this->expectExceptionCode(404);
    $this->storeWorkflow->addBranch($this->mainStore->id, 'invalid target store id');
  }

  public function testCreateBranchFailedDueToSameStoreId()
  {
    $this->expectException(WorkflowException::class);
    $this->expectExceptionCode(412);
    $this->expectExceptionMessage('Branch store id can not be same as main store id.');
    
    $anotherStore = factory(Store::class)->create();
    $this->storeWorkflow->addBranch($this->mainStore->id, $this->mainStore->id);
  }

  public function testCreateBranchFailedDueToMainStoreIdNotfound()
  {
    $this->expectException(WorkflowException::class);
    $this->expectExceptionCode(404);
    $this->storeWorkflow->addBranch('invalid main store id', $this->mainStore->id);
  }

  public function testCreateBranchFailedDueToBranchStoreIdNotfound()
  {
    $this->expectException(WorkflowException::class);
    $this->expectExceptionCode(404);
    $this->storeWorkflow->addBranch($this->mainStore->id, 'invalid branch store id');
  }

  public function testvalidateStoreData()
  {
    $this->assertTrue($this->storeWorkflow->validateStoreData([ 'name' => 'x' ]) === true);
    $this->assertTrue($this->storeWorkflow->validateStoreData([ 'name' => 'abc' ]) === true);
  }

  public function testvalidateStoreDataFailed()
  {
    $this->assertTrue($this->storeWorkflow->validateStoreData([ 'name' => '   ' ]) !== true);
    $this->assertTrue($this->storeWorkflow->validateStoreData([ 'name' => '' ]) !== true);
    $this->assertTrue($this->storeWorkflow->validateStoreData([ ]) !== true);
    $this->assertTrue($this->storeWorkflow->validateStoreData([ 'not_name' => '']) !== true);
  }

  private function createAnotherStoreBranch(){
    $anotherStore = factory(Store::class)->create();
    $anotherStoreBranches = factory(Store::class, 2)->create(['parent_store_id' => $anotherStore->id]);

    $this->anotherStore = $anotherStore;
    $this->anotherStoreBranches = $anotherStoreBranches;
  }

  public function testIsFormCycleFalse() 
  {
    $this->createAnotherStoreBranch();
    $this->assertFalse($this->storeWorkflow->isFormCycle($this->mainStore, $this->anotherStore));
  }

  public function testIsFormCycleTrueMainIsDescendantOfBranchStore() 
  {
    $this->createAnotherStoreBranch();
    $this->mainStore->parent_store_id = $this->anotherStoreBranches[0]->id;
    $this->mainStore->save();

    $this->assertTrue($this->storeWorkflow->isFormCycle($this->mainStore, $this->anotherStore));
  }

  public function testIsFormCycleTrueMainIsChildOfBranchStore() 
  {
    $this->createAnotherStoreBranch();
    $this->mainStore->parent_store_id = $this->anotherStore->id;
    $this->mainStore->save();

    $this->assertTrue($this->storeWorkflow->isFormCycle($this->mainStore, $this->anotherStore));
  }

}
