<?php


namespace OGSoft\ValueObjects\Interfaces;


use OGSoft\ValueObjects\Exceptions\ValidatorException;
use Throwable;

interface ValueObjectInterface
{

	/**
	 * Init object from array or object
	 *
	 * @param object|array $data
	 * @return self
	 * @throws ValidatorException
	 */
	public function init($data): self;

	/**
	 * setter for attribute of ValueObject
	 *
	 * @param string $attrName
	 * @param string $attrSetter
	 * @param $attrValue
	 * @param object $valueObject
	 * @return self
	 * @throws ValidatorException
	 */
	public function setValue(string $attrName, string $attrSetter, $attrValue, object $valueObject): self;

	/**
	 * @param Throwable $throwable
	 * @param string $attrName
	 * @param string $attrSetter
	 * @param $attrValue
	 * @param object $valueObject
	 * @return mixed
	 */
	public function getAttributeErrorMessage(Throwable $throwable, string $attrName, string $attrSetter, $attrValue, object $valueObject): string;

	/**
	 * @param Throwable $throwable
	 * @param string $attrName
	 * @param string $attrSetter
	 * @param $attrValue
	 * @param object $valueObject
	 * @return int
	 */
	public function getAttributeErrorCode(Throwable $throwable, string $attrName, string $attrSetter, $attrValue, object $valueObject): int;

	/**
	 * transform instance to array
	 * @return array
	 */
	public function toArray(): array;


	/**
	 * setter for undefined attributes
	 *
	 * @param string $attrName
	 * @param string $attrSetter
	 * @param $attrValue
	 * @param object $valueObject
	 * @return mixed
	 * @throws ValidatorException
	 */
	public function handleUndefinedAttribute(string $attrName, string $attrSetter, $attrValue, object $valueObject): void;

	/**
	 * set attribute touch
	 *
	 * @param string $attrName
	 * @return self
	 */
	public function setTouched(string $attrName): self;

	/**
	 * check if attribute touched
	 *
	 * @param string $attrName
	 * @return bool
	 */
	public function isTouched(string $attrName): bool;

	/**
	 * get all touched attributes
	 *
	 * @return array
	 */
	public function getTouchedAll(): array;

	/**
	 * check if attribute is ignored
	 *
	 * @param string $attrName
	 * @return bool
	 */
	public function isIgnoredAttr(string $attrName): bool;
}