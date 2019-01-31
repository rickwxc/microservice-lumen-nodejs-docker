<?php

namespace App\Http\Controllers;
use App\Store;

class StoresController extends Controller
{
  public function index()
  {
    return Store::active()->get();
  }

  public function show($id)
  {
    return Store::active()->findOrFail($id);
  }
}
