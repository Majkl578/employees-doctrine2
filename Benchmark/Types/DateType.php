<?php

namespace Benchmark\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateType as DoctrineDateType;
use Nette\DateTime as NetteDateTime;

/**
 * @author Michael Moravec
 */
class DateType extends DoctrineDateType
{
	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		if ($value === NULL) {
			return NULL;
		} elseif ($value instanceof \DateTime) {
			return NetteDateTime::from($value);
		}

		$val = NetteDateTime::createFromFormat('!'.$platform->getDateFormatString(), $value);
		if (!$val) {
			throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateFormatString());
		}
		return NetteDateTime::from($val);
	}
}
