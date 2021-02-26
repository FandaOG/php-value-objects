<?php

namespace OGSoft\ValueObjects;

use OGSoft\ValueObjects\Exceptions\ValidatorException;
use OGSoft\ValueObjects\Interfaces\GlobalValidatorInterface;
use OGSoft\ValueObjects\Interfaces\ValueObjectInterface;
use Throwable;

abstract class AbstractValueObject implements ValueObjectInterface
{
	private $touched = [];
	private $ignoredAttrs = ["ignoredAttrs", "touched"];

	public function isIgnoredAttr(string $attrName): bool
	{
		return in_array($attrName, $this->ignoredAttrs);
	}

	/**
	 * @inheritDoc
	 */
	public function setTouched(string $attrName): void
	{
		$this->touched[$attrName] = true;
	}

	/**
	 * @inheritDoc
	 */
	public function isTouched(string $attrName): bool
	{
		return $this->touched[$attrName];
	}

	/**
	 * @inheritDoc
	 */
	public function getTouchedAll(): array
	{
		return $this->touched;
	}

	/**
	 * @inheritDoc
	 */
	public function init($data): void
	{
		if (is_object($data)) {
			$data = get_object_vars($data);
		}
		// foreach get data
		foreach ($data as $name => $item) {
			$setter = "set" . ucfirst($name);
			$value = $data[$name] ?? null;
			$this->setTouched($name);
			try {
				if (method_exists($this, $setter)) {
					$this->setValue($name, $setter, $value, $this);
				} else {
					// attribute setter is missing
					$this->handleUndefinedAttribute($name, $setter, $value, $this);
				}
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
	 * @inheritDoc
	 */
	public function getAttributeErrorCode(Throwable $throwable, string $attrName, string $attrSetter, $attrValue, object $valueObject): int
	{
		return 0;
	}

	/**
	 * @inheritDoc
	 */
	public function toArray(): array
	{
		$arr = get_object_vars($this);
		$out = [];
		foreach ($arr as $name => $item) {
			if ($this->isIgnoredAttr($name)) {
				continue;
			}
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