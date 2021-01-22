<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\ValueObjects\CarValueObject;

final class ValueObjectTest extends TestCase
{
	public function testTest(): void
	{

		$data = [
			"name" => "My car",
			"enginePower" => "1000",
			"wheels" => [
				["size" => 5],
				["size" => null],
				[],
				["size" => 6, "tire" => ["winter" => true]],
			]
		];

		$car = new CarValueObject();
		$car->init($data);

//		var_dump($car);

		$expactOutput = [
			"name" => "My car",
			"enginePower" => "1000",
			"wheels" => [
				["size" => 5, "tire" => null],
				["size" => null, "tire" => null],
				["size" => null, "tire" => null],
				["size" => 6, "tire" => ["winter" => true]],
			]
		];

		$this->assertEquals($expactOutput, $car->toArray());
	}
}

