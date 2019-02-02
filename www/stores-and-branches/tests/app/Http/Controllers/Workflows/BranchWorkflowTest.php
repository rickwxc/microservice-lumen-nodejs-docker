<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Store;
use App\Http\Controllers\Workflows\BranchWorkflow;
use App\Http\Exceptions\WorkflowException;

class BranchWorkflowTest extends TestCase
{
  use DatabaseMigrations;

  private $mainStore;
  private $branchWorkflow;
	public function setUp()
	{
		parent::setUp();
    $this->branchWorkflow = new BranchWorkflow();

    $mainStore = factory(Store::class)->create();
    $branches = factory(Store::class, 2)->create(['parent_store_id' => $mainStore->id]);
    $this->mainStore = $mainStore;
	}

	public function tearDown()
	{
		parent::tearDown();
	}

  public function testMergeBranchFailedDueToSameStoreId()
  {
    $this->expectException(WorkflowException::class);
    $this->expectExceptionCode(412);
    $this->expectExceptionMessage('Can not merge same store.');
    
    $fromStoreId = $targetStoreId = $this->mainStore->id;
    $this->branchWorkflow->mergeBranch($fromStoreId, $targetStoreId);
  }

  public function testMergeBranchFailedDueToFromStoreIdNotFound()
  {
    $this->expectException(WorkflowException::class);
    $this->expectExceptionCode(404);
    $this->branchWorkflow->addBranch('invalid from store id', $this->mainStore->id);
  }

  public function testMergeBranchFailedDueToTargetStoreIdNotFound()
  {
    $this->expectException(WorkflowException::class);
    $this->expectExceptionCode(404);
    $this->branchWorkflow->addBranch($this->mainStore->id, 'invalid target store id');
  }

  public function testCreateBranchFailedDueToSameStoreId()
  {
    $this->expectException(WorkflowException::class);
    $this->expectExceptionCode(412);
    $this->expectExceptionMessage('Branch store id can not be same as main store id.');
    
    $anotherStore = factory(Store::class)->create();
    $this->branchWorkflow->addBranch($this->mainStore->id, $this->mainStore->id);
  }

  public function testCreateBranchFailedDueToMainStoreIdNotfound()
  {
    $this->expectException(WorkflowException::class);
    $this->expectExceptionCode(404);
    $this->branchWorkflow->addBranch('invalid main store id', $this->mainStore->id);
  }

  public function testCreateBranchFailedDueToBranchStoreIdNotfound()
  {
    $this->expectException(WorkflowException::class);
    $this->expectExceptionCode(404);
    $this->branchWorkflow->addBranch($this->mainStore->id, 'invalid branch store id');
  }
}
