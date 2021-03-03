<?php


namespace Tests\ValueObjects;


class RootValueObject extends AbstractMyValueObject
{
	/**
	 * @var CarValueObject
	 */
	protected $root;

	/**
	 * @return CarValueObject
	 */
	public function getRoot(): CarValueObject
	{
		return $this->root;
	}

	/**
	 * @param CarValueObject $root
	 * @return RootValueObject
	 */
	public function setRoot(CarValueObject $root): RootValueObject
	{
		$this->root = $root;
		return $this;
	}
}