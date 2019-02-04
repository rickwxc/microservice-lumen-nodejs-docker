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

  public function __construct(StoreWorkflow $storeWorkflow, FractalResponse $fractal) 
  {
    parent::__construct($fractal);

    $this->storeWorkflow = $storeWorkflow;
  }

  public function getBranch($storeId)
  {
    try {
      $branches = $this->storeWorkflow->getBranch($storeId);
    } catch (WorkflowException $e) {
      return $this->response_error($e->getMessage(), $e->getCode());
    }

    return $this->collection($branches, new StoreTransformer());
  }

  public function addBranch(Request $request, $storeId)
  {
    try {
      $branchStore = $this->storeWorkflow->addBranch($storeId, $request->branchStoreId);
    } catch (WorkflowException $e) { 
      return $this->response_error($e->getMessage(), $e->getCode());
    }

		return $this->item($branchStore, new StoreTransformer());
  }
}
