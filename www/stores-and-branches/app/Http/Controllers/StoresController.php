<?php

namespace App\Http\Controllers;
use App\Store;

use Illuminate\Http\Request;
use App\Transformer\StoreTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use App\Http\Response\FractalResponse;
use App\Http\Controllers\Workflows\StoreWorkflow;
use App\Http\Exceptions\WorkflowException;


class StoresController extends Controller
{

  private $storeWorkflow;

  protected function after_construct()
  {
    $this->storeWorkflow = new StoreWorkflow();
  }

  public function index()
  {
    $stores = $this->storeWorkflow->allStores();
    return $this->collection($stores, new StoreTransformer());
  }

  public function show(Request $request, $id)
  {
    $include = $request->get('include');
    try{
      $store = $this->storeWorkflow->getStoreById($id, $include);
		} catch (WorkflowException $e) {
      return $this->response_error($e->getMessage(), $e->getCode());
    }

    return $this->item($store, new StoreTransformer());
  }

  public function create(Request $request)
  {
		$this->validate_rules($request);

    $store = Store::create($request->all());
    $data = $this->item($store, new StoreTransformer());
    return response()->json($data, 201, [
      'Location' => route('stores.show', ['id' => $store->id])
    ]);
  }

  public function update(Request $request, $id)
  {
		try {
      $store = Store::findOrFail($id);
		} catch (ModelNotFoundException $e) {
      return $this->response_error('Store not found', 404);
		}

		$this->validate_rules($request);

		$store->fill($request->all());
		$store->save();

		return $this->item($store, new StoreTransformer());
  }

	protected function validate_rules(Request $request)
	{
		$this->validate($request, [
			'name' => 'required|max:255',
		], [
			'name.required' => 'Please provide a :attribute.'
		]);
	}

  public function destroy($id) 
  {
    try{
      $this->storeWorkflow->processDelete($id);
		} catch (WorkflowException $e) {
      return $this->response_error($e->getMessage(), $e->getCode());
		}
    return response(null, 204); 
  }

  //merge from store into target store
  public function merge(Request $request, $targetStoreId)
  {
    $fromStoreId = ($request->fromStoreId);

    try {
      $fromStore = $this->storeWorkflow->mergeBranch($fromStoreId, $targetStoreId);
    } catch (WorkflowException $e) { 
      return $this->response_error($e->getMessage(), $e->getCode());
    }

		return $this->item($fromStore, new StoreTransformer());
  }
}
