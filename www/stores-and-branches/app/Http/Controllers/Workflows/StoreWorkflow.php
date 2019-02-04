<?php

namespace App\Http\Controllers\Workflows;
use App\Store;
use Validator;
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

    if($this->isFormCycle($targetStore, $fromStore)){
      throw new WorkflowException("Target store is fromStore's child or descendants.", 405);
    }

    $fromStore->parent_store_id = $targetStore->id;
    $fromStore->save();
    return $fromStore;
  }

  public function isFormCycle($mainStore, $branchStore)
  {
    $branchStoreIds = [$branchStore->id];
    $branchStoreDescendants = $branchStore->descendants();
    foreach($branchStoreDescendants as $store){
      $branchStoreIds[] = $store->id;
    }

    return in_array($mainStore->parent_store_id, $branchStoreIds);
  }

  public function addBranch($storeId, $branchStoreId)
  {
    if ($branchStoreId == $storeId)
    {
      throw new WorkflowException('Branch store id can not be same as main store id.', 412);
    }

    try{
      $mainStore = Store::findOrFail($storeId);
      $branchStore = Store::findOrFail($branchStoreId);
    } catch (ModelNotFoundException $e) { 
      throw new WorkflowException('Main store or branch store not found.', 404);
    }

    if($this->isFormCycle($mainStore, $branchStore)){
      throw new WorkflowException("Main store is already branch store's child or descendants.", 405);
    }

    $branchStore->parent_store_id = $storeId;
		$branchStore->save();

    return $branchStore;
  }

  public function allStores()
  {
    $rootStores = Store::rootStores()->get();
    foreach($rootStores as $store){
      $store->branches = $store->descendants();
    }

    return $rootStores;
  }

  public function validateStoreData($storeMassData){
    $validator = Validator::make($storeMassData, [
      'name' => 'required|max:255',
    ], [
      'name.required' => 'Please provide a :attribute.'
    ]);

    if ($validator->fails()) {
      return $validator->errors()->toArray();
    }
    return true;
  }

  public function updateStore($storeId, $storeMassData)
  {
		try {
      $store = Store::findOrFail($storeId);
		} catch (ModelNotFoundException $e) {
      throw new WorkflowException('Store not found.', 404);
		}

    if(($error = $this->validateStoreData($storeMassData)) !== true){
      throw new WorkflowException('Store update failed.', 422);
    }

		$store->fill($storeMassData);
		$store->save();

    return $store;
  }

  public function createStore($storeMassData)
  {
    if(($error = $this->validateStoreData($storeMassData)) !== true){
      //todo: send error message to exception
      throw new WorkflowException('Store create failed.', 422);
    }

    $store = Store::create($storeMassData);
    return $store;
  }

  public function getStoreById($storeId, $include = false)
  {
    try
    {
      $store = Store::findOrFail($storeId);
    } catch (ModelNotFoundException $e) { 
      throw new WorkflowException('Store not found.', 404);
    }

    if($include == 'children')
    {
      $store->branches = $store->children()->get();
    }else if($include == 'descendant')
    {
      $store->branches = $store->descendants();
    }
    return $store;
  }
}
