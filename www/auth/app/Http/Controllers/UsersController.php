<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;

class UsersController extends Controller
{

  public function register(Request $request)
  {
    $this->validate($request, [
      'name' => 'required|max:255',
      'email' => 'required',
      'password' => 'required',
    ]);

    $email = $request->input('email');
    $name = $request->input('name');
    $hasher = app()->make('hash');
    $password = $hasher->make($request->input('password'));

    $user = User::create([
      'name' => $name,
      'email' => $email,
      'password' => $password,
    ]);

    return response()->json(['user' => $user], 201);
  }

  public function test(Request $request)
  {
    if ($request->wantsJson()) 
    {
      return response()->json(['hello' => 'stranger']);
    }
    return response()->make('Hello stranger', 200, ['Content-Type' => 'text/plain']);
  }
}
