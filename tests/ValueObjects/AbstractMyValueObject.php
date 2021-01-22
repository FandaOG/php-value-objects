<?php

namespace Tests\ValueObjects;

use OG\ValueObjects\AbstractValueObject;
use OG\ValueObjects\Traits\InitAndValidateTrait;
use Throwable;

class AbstractMyValueObject extends AbstractValueObject
{
	use InitAndValidateTrait;

	public function getAttributeErrorMessage(Throwable $throwable, string $attrName, string $attrSetter, $attrValue, object $valueObject): string
	{
		return "Error " . $attrName . " in " . get_class($valueObject) . " data <" . print_r($attrValue, true) . ">";
	}
}