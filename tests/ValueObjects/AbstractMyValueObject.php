<?php

namespace Tests\ValueObjects;

use OGSoft\ValueObjects\AbstractValueObject;
use Throwable;

abstract class AbstractMyValueObject extends AbstractValueObject
{
	public function getAttributeErrorMessage(Throwable $throwable, string $attrName, string $attrSetter, $attrValue, object $valueObject): string
	{
		return "Error " . $attrName . " in " . get_class($valueObject) . " data <" . print_r($attrValue, true) . ">";
	}

	public function handleUndefinedAttribute(string $attrName, string $attrSetter, $attrValue, object $valueObject): void
	{
		// ignore
		return;
	}
}