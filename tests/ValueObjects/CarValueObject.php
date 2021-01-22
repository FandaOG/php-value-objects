<?php

namespace Tests\ValueObjects;

class CarValueObject extends AbstractMyValueObject
{
	/**
	 * @var string car name
	 */
	protected $name;

	/**
	 * @var int engine power
	 */
	protected $enginePower;

	/**
	 * @var WheelValueObject[]
	 */
	protected $wheels;

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return CarValueObject
	 */
	public function setName(string $name): CarValueObject
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getEnginePower(): int
	{
		return $this->enginePower;
	}

	/**
	 * @param int $enginePower
	 * @return CarValueObject
	 */
	public function setEnginePower(int $enginePower): CarValueObject
	{
		$this->enginePower = $enginePower;
		return $this;
	}

	/**
	 * @return WheelValueObject[]
	 */
	public function getWheels(): array
	{
		return $this->wheels;
	}

	/**
	 * @param WheelValueObject[] $wheels
	 * @return CarValueObject
	 */
	public function setWheels(WheelValueObject ...$wheels): CarValueObject
	{
		$this->wheels = $wheels;
		return $this;
	}

}