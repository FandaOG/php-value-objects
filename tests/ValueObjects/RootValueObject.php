<?php


namespace Tests\ValueObjects;


class RootValueObject extends AbstractMyValueObject
{
	/**
	 * @var TireValueObject
	 */
	protected $root;

	/**
	 * @return TireValueObject
	 */
	public function getRoot(): TireValueObject
	{
		return $this->root;
	}

	/**
	 * @param TireValueObject $root
	 * @return RootValueObject
	 */
	public function setRoot(TireValueObject $root): RootValueObject
	{
		$this->root = $root;
		return $this;
	}
}