<?php

namespace App\Http\Controllers;
use App\Store;

use Illuminate\Http\Request;
use App\Transformer\StoreTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use App\Http\Response\FractalResponse;
use App\Http\Controllers\Workflows\BranchWorkflow;
use App\Http\Exceptions\WorkflowException;


class StoresController extends Controller
{

  private $branchWorkflow;

  protected function after_construct()
  {
    $this->branchWorkflow = new BranchWorkflow();
  }

  public function index()
  {
    return $this->collection(Store::active()->get(), new StoreTransformer());
  }

  public function show($id)
  {
    try{
      $store = Store::active()->findOrFail($id);
    } catch (ModelNotFoundException $e) { 
      return $this->response_error('Store not found', 404);
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
      $store = Store::active()->findOrFail($id);
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
      $store = Store::active()->findOrFail($id);
		} catch (ModelNotFoundException $e) {
      return $this->response_error('Store not found', 404);
		}
    $store->status = Store::PENDING_DELETE;
    $store->save();
    return response(null, 204); 
  }

  //merge from store into target store
  public function merge(Request $request, $targetStoreId)
  {
    $fromStoreId = ($request->fromStoreId);

    try {
      $fromStore = $this->branchWorkflow->mergeBranch($fromStoreId, $targetStoreId);
    } catch (WorkflowException $e) { 
      return $this->response_error($e->getMessage(), $e->getCode());
    }

		return $this->item($fromStore, new StoreTransformer());
  }
}
