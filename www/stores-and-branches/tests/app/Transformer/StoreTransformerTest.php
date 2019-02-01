<?php
namespace Tests\App\Transformer;

use TestCase;
use App\Store;
use App\Transformer\StoreTransformer;
use League\Fractal\TransformerAbstract;
use Laravel\Lumen\Testing\DatabaseMigrations;

class StoreTransformerTest extends TestCase {
	use DatabaseMigrations;

	public function testItCanBeInitialized()
	{
		$subject = new StoreTransformer();
		$this->assertInstanceOf(TransformerAbstract::class, $subject);
	} 

	public function testItTransformsOneStoreModel() {
		$store = factory(Store::class)->create();
		$subject = new StoreTransformer();

		$transform = $subject->transform($store);

		$this->assertArrayHasKey('id', $transform);
		$this->assertArrayHasKey('name', $transform);
		$this->assertArrayHasKey('created', $transform);
		$this->assertArrayHasKey('updated', $transform);
	}

}

