<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name'
  ];

  public function children() {
    return self::where('parent_store_id', '=', $this->id);
  }

}
