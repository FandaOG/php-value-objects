<?php

namespace OGSoft\ValueObjects;

use Carbon\Carbon;
use DateTime;
use Exception;
use OGSoft\ValueObjects\Exceptions\ValidatorException;
use OGSoft\ValueObjects\Interfaces\GlobalValidatorInterface;
use OGSoft\ValueObjects\Interfaces\ValueObjectInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use ReflectionType;
use Throwable;

abstract class AbstractValueObject implements ValueObjectInterface
{
	private array $touched = [];
	private array $ignoredAttrs = ["ignoredAttrs", "touched"];

	public function isIgnoredAttr(string $attrName): bool
	{
		return in_array($attrName, $this->ignoredAttrs);
	}

	/**
	 * @inheritDoc
	 */
	public function setTouched(string $attrName): self
	{
		$this->touched[$attrName] = true;
		return $this;
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
	public function init($data): self
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

		return $this;
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

	public function setValue(string $attrName, string $attrSetter, $attrValue, object $obj): self
	{
		$reflectionParameter = self::getReflectionParameter($obj, $attrSetter);
		// build-in types
		if ($reflectionParameter->getType()->isBuiltin()) {
			if (is_array($attrValue)) {
				$attrValue = array_map(function ($v) use ($reflectionParameter) {
					return self::transformBuildInType($reflectionParameter, $v);
				}, $attrValue);
				$obj->$attrSetter(...$attrValue);
			} else {
				$attrValue = self::transformBuildInType($reflectionParameter, $attrValue);
				$obj->$attrSetter($attrValue);
			}
		} // custom types
		else {
			self::initAndValidateValueObject($obj, $attrSetter, $reflectionParameter, $attrValue);
		}

		return $this;
	}

	/**
	 * @param $obj
	 * @param $setter
	 * @return null|ReflectionParameter
	 * @throws ReflectionException
	 */
	private static function getReflectionParameter($obj, $setter): ?ReflectionParameter
	{
		$reflectionClass = new ReflectionClass($obj);
		$params = $reflectionClass->getMethod($setter)->getParameters();
		// setter has only one param
		$param = $params[0];
		if ($param->getType() instanceof ReflectionType) {
//      $name = $param->getType()->getName();
			return $param;
		}
		return null;
	}

	/**
	 * Normalize build-in type
	 *
	 * @param ReflectionParameter $reflectionParameter
	 * @param $value
	 * @return mixed
	 */
	private static function transformBuildInType(ReflectionParameter $reflectionParameter, $value)
	{
		// bool transform
		if ($reflectionParameter->getType()->getName() == "bool") {
			if (is_bool($value) || is_null($value)) {
				return $value;
			} else {
				return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
			}
		}

		// int transform
		if ($reflectionParameter->getType()->getName() == "int") {
			if ($value === '') {
				return null; // set null on empty string (because of Mango API and types handling)
			}
		}

		// check null
		if (!$reflectionParameter->allowsNull() && empty($value)) {
			throw new Exception("Wrong data type of property. Data can not be null");
		}

		// general
		return $value;
	}

	/**
	 * @param object $obj
	 * @param string $setter
	 * @param ReflectionParameter $parameter
	 * @param $data
	 * @throws Exception
	 */
	private static function initAndValidateValueObject(object $obj, string $setter, ReflectionParameter $parameter, $data)
	{
		$type = $parameter->getType()->getName();
		if (is_subclass_of($type, AbstractValueObject::class)) {
			if (empty($data) && !is_array($data)) {
				if ($parameter->allowsNull()) {
					return;
				}
				throw new ValidatorException(" Wrong data type of property. Data can not be null", get_class($obj), $parameter->name, $data);
			}
			if ($parameter->isVariadic()) {
				if (!is_array($data)) {
					$obj->$setter(null);
					return;
				}
				$dataArr = [];
				foreach ($data as $objData) {
					$reqObj = self::createValueObject($type, $objData);
					if ($reqObj) {
						$dataArr[] = $reqObj;
					}
				}
				$obj->$setter(...$dataArr);
			} else {
				$reqObj = self::createValueObject($type, $data);
				$obj->$setter($reqObj);
			}
			return;
		} elseif ($type == DateTime::class || $type == Carbon::class) {
			if (!$parameter->allowsNull() && empty($data)) {
				throw new ValidatorException(" Wrong data type of property. Date can not be null", get_class($obj), $parameter->name, $data);
			}
			/**
			 * Parse date and time from data, if timezone is not part of the data string, user setting timezone is used (eg. messages from
			 * machines come in UTC and without timezone information). If the timezone is part of the string, this one is used and has higher
			 * priority, than user settings.
			 * If the timezone is not specified within data string, nor in the user settings, the application default timezone is used.
			 */
			// TODO add timezone support
			$dateTime = empty($data) ? null : Carbon::parse($data, /*getCustomUserTimezone()*/);
			//$dateTime->setTimezone(new \DateTimeZone(config("app.timezone")));

			$obj->$setter($dateTime);
			return;
		}
		throw new Exception("Unknown array type. Type has to be build in or subclass of AbstractRequestBody - " . $setter);
	}

	/**
	 * @param $className
	 * @param $data
	 * @return false|AbstractValueObject
	 * @throws ValidatorException
	 */
	private static function createValueObject($className, $data)
	{
		$valueObject = new $className();
		if ($valueObject instanceof AbstractValueObject) {
			$valueObject->init($data);
			return $valueObject;
		}
		return false;
	}
}