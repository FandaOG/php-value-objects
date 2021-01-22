<?php

namespace Tests\ValueObjects;

class TireValueObject extends AbstractMyValueObject
{
	/**
	 * @var bool
	 */
	protected $winter;

	/**
	 * @return bool
	 */
	public function isWinter(): bool
	{
		return $this->winter;
	}

	/**
	 * @param bool $winter
	 * @return TireValueObject
	 */
	public function setWinter(bool $winter): TireValueObject
	{
		$this->winter = $winter;
		return $this;
	}

}