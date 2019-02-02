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
    'status' => self::ACTIVE
  ];

  public function children() {
    return self::active()->where('parent_store_id', '=', $this->id);
  }

  public function parent() {
    return $this->belongsTo('Store','parent_store_id');
  }

  public function scopeActive($query)
  {
    return $query->where('status', '=', self::ACTIVE);
  }
}
