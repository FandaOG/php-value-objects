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
	 * @param object $valueObject
	 * @return mixed
	 * @throws ValidatorException
	 */
	abstract public function setValue(string $attrName, string $attrSetter, $attrValue, object $valueObject);

	/**
	 * @param Throwable $throwable
	 * @param string $attrName
	 * @param string $attrSetter
	 * @param $attrValue
	 * @param object $valueObject
	 * @return mixed
	 */
	abstract public function getAttributeErrorMessage(Throwable $throwable, string $attrName, string $attrSetter, $attrValue, object $valueObject): string;

	/**
	 * @param object|array $data
	 * @return void
	 * @throws ValidatorException
	 */
	public function init($data): void
	{
		if (is_object($data)) {
			$data = get_object_vars($data);
		}
		foreach ($data as $name => $item) {
			$setter = "set" . ucfirst($name);
			$value = $data[$name] ?? null;
			try {
				$this->setValue($name, $setter, $value, $this);
			} catch (Throwable $e) {
				$msg = $this->getAttributeErrorMessage($e, $name, $setter, $value, $this);
				$code = $this->getAttributeErrorCode($e, $name, $setter, $value, $this);
				throw new ValidatorException($msg, get_class($this), $name, $value, $code, $e);
			}
		}

		if ($this instanceof GlobalValidatorInterface) {
			$this->globalValidate();
		}
	}

	/**
	 * @param Throwable $throwable
	 * @param string $attrName
	 * @param string $attrSetter
	 * @param $attrValue
	 * @param object $valueObject
	 * @return int
	 */
	public function getAttributeErrorCode(Throwable $throwable, string $attrName, string $attrSetter, $attrValue, object $valueObject): int
	{
		return 0;
	}

	/**
	 * Transform value object to array
	 *
	 * @return array
	 */
	public function toArray(): array
	{
		$arr = get_object_vars($this);
		$out = [];
		foreach ($arr as $name => $item) {
			// ValueObject element
			if ($item instanceof AbstractValueObject) {
				$out[$name] = $item->toArray();
				continue;
			}
			if (is_array($item)) {
				foreach ($item as $i) {
					// array of ValueObjects
					if ($i instanceof AbstractValueObject) {
						$out[$name][] = $i->toArray();
					} // array of other types
					else {
						$out[$name][] = $i;
					}
				}
				continue;
			}

			// other types
			$out[$name] = $item;
		}
		return $out;
	}

}