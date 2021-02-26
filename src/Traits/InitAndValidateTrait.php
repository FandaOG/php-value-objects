<?php


namespace OGSoft\ValueObjects\Traits;


use Carbon\Carbon;
use DateTime;
use Exception;
use OGSoft\ValueObjects\AbstractValueObject;
use OGSoft\ValueObjects\Exceptions\ValidatorException;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use ReflectionType;

trait InitAndValidateTrait
{
	public function setValue(string $attrName, string $attrSetter, $attrValue, object $obj): void
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