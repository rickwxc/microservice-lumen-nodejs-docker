<? 
namespace App\Transformer;

use App\Store;
use League\Fractal\TransformerAbstract;

class StoreTransformer extends TransformerAbstract {

  public function transform(Store $store)
  {
    return [
      'id' => $store->id,
      'name' => $store->name,
      'parent_store_id' => (int)$store->parent_store_id,
      'branches' => ['data' => $store->branches],
      'created' => $store->created_at->toIso8601String(),
      'updated' => $store->updated_at->toIso8601String(),
    ];
  }
}
