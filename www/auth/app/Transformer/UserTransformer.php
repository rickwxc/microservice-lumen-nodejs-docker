<?php
namespace App\Transformer;

use League\Fractal\TransformerAbstract;
use App\User;

class UserTransformer extends TransformerAbstract 
{

  public function transform(User $user)
  {
    return [
      'id'          => $user->id,
      'name'       => $user->name,
      'created' => $user->created_at->toIso8601String(),
      'updated' => $user->updated_at->toIso8601String(),
    ];
  } 

}
