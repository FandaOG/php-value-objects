<?php
declare(strict_types=1);

namespace Tests;

use OGSoft\ValueObjects\Exceptions\ValidatorException;
use PHPUnit\Framework\TestCase;
use Tests\ValueObjects\CarValueObject;

final class ValueObjectTest extends TestCase
{
	public function testTest(): void
	{

		$data = [
			"name" => "My car",
			"enginePower" => "1000",
			"enginePower1" => "1000",
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

		$this->assertTrue($car->isTouched("name"));
	}

	public function testNull()
	{
		$data = [
			"name" => "My car",
			// test null value
			"enginePower" => null,
			"wheels" => [
				["size" => 5],
				["size" => null],
				[],
				["size" => 6, "tire" => ["winter" => true]],
			]
		];

		$car = new CarValueObject();
		$this->expectException(ValidatorException::class);
		$car->init($data);
		$this->assertTrue(true);
	}
}

