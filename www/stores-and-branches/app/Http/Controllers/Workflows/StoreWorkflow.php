<?php

namespace App\Http\Controllers\Workflows;
use App\Store;
use App\Http\Exceptions\WorkflowException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;


class StoreWorkflow
{
  public function processDelete($storeId)
  {
    DB::beginTransaction();

    try{
      $this->deleteStore($storeId);
    } catch (WorkflowException $e) {
      DB::rollBack();
      throw $e;
    }
    DB::commit();
  }

  private function deleteStore($storeId)
  {
    try{
      $store = Store::findOrFail($storeId);
    } catch (ModelNotFoundException $e) {
      throw new WorkflowException('Store not found.', 404);
    }

    $branches = $store->children()->get();
    foreach($branches as $branch){
      $this->deleteStore($branch->id);
    }
    $store->delete();
  }

  public function mergeBranch($fromStoreId, $targetStoreId)
  {
    if ($fromStoreId == $targetStoreId)
    {
      throw new WorkflowException('Can not merge same store.', 412);
    }

    try{
      $targetStore = Store::findOrFail($targetStoreId);
      $fromStore = Store::findOrFail($fromStoreId);
    } catch (ModelNotFoundException $e) { 
      throw new WorkflowException('Stores not found.', 404);
    }
    $fromStore->parent_store_id = $targetStore->id;
    $fromStore->save();
    return $fromStore;
  }


  public function addBranch($storeId, $branchStoreId)
  {
    if ($branchStoreId == $storeId)
    {
      throw new WorkflowException('Branch store id can not be same as main store id.', 412);
    }

    try{
      $store = Store::findOrFail($storeId);
      $branchStore = Store::findOrFail($branchStoreId);
    } catch (ModelNotFoundException $e) { 
      throw new WorkflowException('main store or branch store not found.', 404);
    }
    $branchStore->parent_store_id = $storeId;
		$branchStore->save();

    return $branchStore;
  }
}
