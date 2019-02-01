<?php

namespace App\Http\Controllers;
use App\Store;

use Illuminate\Http\Request;
use App\Transformer\StoreTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use App\Http\Response\FractalResponse;


class StoresController extends Controller
{
  public function index()
  {
    return $this->collection(Store::active()->get(), new StoreTransformer());
  }

  public function show($id)
  {
    return $this->item(Store::active()->findOrFail($id), new StoreTransformer());
  }
}
