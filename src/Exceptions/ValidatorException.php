<?php


namespace OGSoft\ValueObjects\Exceptions;


use Exception;
use Throwable;

class ValidatorException extends Exception
{
	/**
	 * @var string
	 */
	private $valueObjectClassName;
	/**
	 * @var string
	 */
	private $attrName;
	/**
	 * @var mixed
	 */
	private $attrValue;

	public function __construct($message, $valueObjectClassName, $attrName, $attrValue, $code = 0, Throwable $previous = null)
	{
		$this->valueObjectClassName = $valueObjectClassName;
		$this->attrName = $attrName;
		$this->attrValue = $attrValue;

		parent::__construct($message, $code, $previous);
	}

	/**
	 * @return string
	 */
	public function getValueObjectClassName(): string
	{
		return $this->valueObjectClassName;
	}

	/**
	 * @param string $valueObjectClassName
	 * @return ValidatorException
	 */
	public function setValueObjectClassName(string $valueObjectClassName): ValidatorException
	{
		$this->valueObjectClassName = $valueObjectClassName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAttrName(): string
	{
		return $this->attrName;
	}

	/**
	 * @param string $attrName
	 * @return ValidatorException
	 */
	public function setAttrName(string $attrName): ValidatorException
	{
		$this->attrName = $attrName;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAttrValue()
	{
		return $this->attrValue;
	}

	/**
	 * @param mixed $attrValue
	 * @return ValidatorException
	 */
	public function setAttrValue($attrValue): ValidatorException
	{
		$this->attrValue = $attrValue;
		return $this;
	}


}