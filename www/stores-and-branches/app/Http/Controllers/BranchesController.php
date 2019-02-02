<?php

namespace App\Http\Controllers;
use App\Store;
use App\Http\Controllers\Workflows\StoreWorkflow;

use Illuminate\Http\Request;
use App\Transformer\StoreTransformer;
use Illuminate\Http\Response;
use App\Http\Response\FractalResponse;
use App\Http\Exceptions\WorkflowException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class BranchesController extends Controller
{
  private $storeWorkflow;

  protected function after_construct()
  {
    $this->storeWorkflow = new StoreWorkflow();
  }

  public function getBranch($storeId)
  {
    try{
      $store = Store::active()->findOrFail($storeId);
		} catch (ModelNotFoundException $e) {
      return $this->response_error('Store not found', 404);
		}
    $branches = $store->children()->get();
    return $this->collection($branches, new StoreTransformer());
  }

  public function addBranch(Request $request, $storeId)
  {
    $branchStoreId = ($request->branch_store_id);

    try {
      $branchStore = $this->storeWorkflow->addBranch($storeId, $branchStoreId);
    } catch (SameStoreIdException $e) { 
      return $this->response_error($e->getMessage(), $e->getCode());
    }

		return $this->item($branchStore, new StoreTransformer());
  }
}
