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
      'status' => $store->status,
      'created' => $store->created_at->toIso8601String(),
      'updated' => $store->updated_at->toIso8601String(),
    ];
  }
}
