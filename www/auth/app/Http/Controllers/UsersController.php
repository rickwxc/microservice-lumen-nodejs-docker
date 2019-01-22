<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use App\Http\Response\FractalResponse;
use App\Transformer\UserTransformer;

class UsersController extends Controller
{

  public function register(Request $request)
  {
    $this->validate($request, [
      'name' => 'required|max:255',
      'email' => 'required',
      'password' => 'required',
    ],
  
    [
    'required' => __('messages.missing_required')
    ]
  );

    $email = $request->input('email');
    $name = $request->input('name');
    $hasher = app()->make('hash');
    $password = $hasher->make($request->input('password'));

    $user = User::create([
      'name' => $name,
      'email' => $email,
      'password' => $password,
    ]);

    return response()->json(
      ['user' => $user], 
      201, 
      ['Location' => '/users/3']
      //['Location' => route('books.show', ['id' => $book->id]
    );
  }


  public function welcome(Request $request)
  {
    return __('messages.welcome');
  }

  public function test(Request $request)
  {
     return $this->collection(User::all(), new UserTransformer());
    //return $this->fractal->collection(User::all(), new UserTransformer());
    // todo here
    //return FractalResponse::collection(User::all(), new UserTransformer());
    return response()->json([
      'data' => User::all()->toArray()
    ]);
  }
}
