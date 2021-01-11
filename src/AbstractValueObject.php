<?php

namespace OG\ValueObjects;

use OG\ValueObjects\Exceptions\ValidatorException;
use OG\ValueObjects\Interfaces\GlobalValidatorInterface;
use Throwable;

abstract class AbstractValueObject
{

	/**
	 * setter for attribute of ValueObject
	 *
	 * @param string $attrName
	 * @param string $attrSetter
	 * @param $attrValue
	 * @param AbstractValueObject $valueObject
	 * @return mixed
	 * @throws ValidatorException
	 */
	abstract public function setValue(string $attrName, string $attrSetter, $attrValue, self $valueObject);

	/**
	 * @param Throwable $throwable
	 * @param string $attrName
	 * @param string $attrSetter
	 * @param $attrValue
	 * @param AbstractValueObject $valueObject
	 * @return mixed
	 */
	abstract public function getAttributeErrorMessage(Throwable $throwable, string $attrName, string $attrSetter, $attrValue, self $valueObject);

	/**
	 * @param AbstractValueObject $valueObject
	 * @return void
	 * @throws ValidatorException
	 */
	public function init(self $valueObject): void
	{
		$arr = get_object_vars($valueObject);
		foreach ($arr as $name => $item) {
			$setter = "set" . ucfirst($name);
			$value = $data[$name] ?? null;
			try {
				$this->setValue($name, $setter, $value, $valueObject);
			} catch (Throwable $e) {
				$msg = $this->getAttributeErrorMessage($e, $name, $setter, $value, $valueObject);
				$code = $this->getAttributeErrorCode($e, $name, $setter, $value, $valueObject);
				throw new ValidatorException($msg, get_class($valueObject), $name, $value, $code, $e);
			}
		}

		if ($valueObject instanceof GlobalValidatorInterface) {
			$valueObject->globalValidate();
		}
	}

	/**
	 * @param Throwable $throwable
	 * @param string $attrName
	 * @param string $attrSetter
	 * @param $attrValue
	 * @param self $valueObject
	 * @return int
	 */
	public function getAttributeErrorCode(Throwable $throwable, string $attrName, string $attrSetter, $attrValue, self $valueObject): int
	{
		return 0;
	}

}