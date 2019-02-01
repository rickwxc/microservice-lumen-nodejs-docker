<?php

namespace Tests\App\Http\Response;

use TestCase;
use Mockery as m;
use League\Fractal\Manager;
use App\Http\Response\FractalResponse;
use League\Fractal\Serializer\SerializerAbstract;


class FractalResponseTest extends TestCase {

	public function testItCanBeInitialized()
	{

		$manager = m::mock(Manager::class);
		$serializer = m::mock(SerializerAbstract::class);

		$manager->shouldReceive('setSerializer')
			->with($serializer)
			->once()
			->andReturn($manager);
		$fractal = new FractalResponse($manager, $serializer); 

		$this->assertInstanceOf(FractalResponse::class, $fractal);
	}

	public function testItCanTransformOneItem()
	{
		$transformer = m::mock('League\Fractal\TransformerAbstract');
		$scope = m::mock('League\Fractal\Scope');
		$scope->shouldReceive('toArray')
			->once()
			->andReturn(['foo' => 'bar']);

		// Serializer
		$serializer = m::mock('League\Fractal\Serializer\SerializerAbstract');
		$manager = m::mock('League\Fractal\Manager');

		$manager->shouldReceive('setSerializer')
			->with($serializer)
			->once();

		$manager->shouldReceive('createData')
			->once()
			->andReturn($scope);
		$subject = new FractalResponse($manager, $serializer); 
		$this->assertInternalType(
			'array',
			$subject->item(['foo' => 'bar'], $transformer)
		);

	}
}
