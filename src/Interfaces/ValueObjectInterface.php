<?php


namespace OG\ValueObjects\Interfaces;


use OG\ValueObjects\Exceptions\ValidatorException;
use Throwable;

interface ValueObjectInterface
{

	/**
	 * Init object from array or object
	 *
	 * @param object|array $data
	 * @return void
	 * @throws ValidatorException
	 */
	public function init($data): void;

	/**
	 * setter for attribute of ValueObject
	 *
	 * @param string $attrName
	 * @param string $attrSetter
	 * @param $attrValue
	 * @param object $valueObject
	 * @return mixed
	 * @throws ValidatorException
	 */
	public function setValue(string $attrName, string $attrSetter, $attrValue, object $valueObject): void;

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
	 * @return void
	 */
	public function setTouched(string $attrName): void;

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