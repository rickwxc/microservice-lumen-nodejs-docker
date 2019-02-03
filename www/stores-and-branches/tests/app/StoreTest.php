<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Store;
use App\Http\Exceptions\WorkflowException;

class StoreTest extends TestCase
{
  use DatabaseMigrations;

  private $mainStore;
	public function setUp()
	{
		parent::setUp();

    $mainStore = factory(Store::class)->create();
    $this->mainStore = $mainStore;
    $this->children = factory(Store::class, 3)->create(['parent_store_id' => $mainStore->id]);
    $this->otherMainStores = factory(Store::class, 5)->create();
	}

	public function tearDown()
	{
		parent::tearDown();
	}

	public function testChildren()
	{
    $branches = $this->mainStore->children()->get()->toArray();

    $this->assertCount(3, $branches);
  }

	public function testDescendantsWhenHasGrandChildren()
	{
    //add 2 more grand children
    $grandchild = factory(Store::class)->create(['parent_store_id' => $this->children[0]->id]);
    $grandgrandchild = factory(Store::class)->create(['parent_store_id' => $grandchild->id]);
    $all_descendants = $this->children;  
    $all_descendants[] = $grandchild;
    $all_descendants[] = $grandgrandchild;

    $branches = $this->mainStore->descendants();

    $this->assertArrayEqual($branches, $all_descendants, 'id');
  }

	public function testDescendantsWhenNoGrandChildren()
	{
    $branches = $this->mainStore->descendants();

    $this->assertArrayEqual($branches, $this->children, 'id');
  }

	public function testRootStores()
	{
    $rootStores = Store::rootStores()->get();
    $this->assertArrayEqual($rootStores, array_merge([$this->mainStore->toArray()], $this->otherMainStores->toArray()), 'id');
  }

}
