<?php

namespace Benchmark\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType as DoctrineDateTimeType;
use Nette\DateTime as NetteDateTime;

/**
 * @author Michael Moravec
 */
class DateTimeType extends DoctrineDateTimeType
{
	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		if ($value === NULL) {
			return NULL;
		} elseif ($value instanceof \DateTime) {
			return NetteDateTime::from($value);
		}

		$val = NetteDateTime::createFromFormat($platform->getDateTimeFormatString(), $value);
		if (!$val) {
			throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());
		}
		return NetteDateTime::from($val);
	}
}
