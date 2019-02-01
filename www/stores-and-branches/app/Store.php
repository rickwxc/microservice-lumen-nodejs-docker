<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
  const ACTIVE = 'active';
  const PENDING_DELETE = 'pending-delete';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name'
  ];

  protected $attributes = [
    'status' => self::ACTIVE,
    'has_branch' => false
  ];

  public function scopeActive($query)
  {
    return $query->where('status', '=', self::ACTIVE);
  }
}
