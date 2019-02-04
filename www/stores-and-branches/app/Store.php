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

  protected $attributes = [
    'parent_store_id' => 0
  ];

  public static function rootStores() {
    return self::where('parent_store_id', '=', 0);
  }

  public function children() {
    return self::where('parent_store_id', '=', $this->id);
  }


  public function descendants() {
    $branches = $this->children()->get();
    $result = [];
    foreach($branches as $branch)
    {
      $result[] = $branch;
      $descendants = $branch->descendants();
      $result = array_merge($result, $descendants);
    }

    return $result;
  }

}
