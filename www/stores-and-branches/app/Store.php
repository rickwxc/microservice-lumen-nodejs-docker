<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
  const ACTIVE = 'active';
  const PENDING_DELETE = 'pending-delete';

  public function scopeActive($query)
  {
    return $query->where('status', '=', Store::ACTIVE);
  }
}
