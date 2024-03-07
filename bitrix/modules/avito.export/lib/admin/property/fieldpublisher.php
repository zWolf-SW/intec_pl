<?php
namespace Avito\Export\Admin\Property;

use Bitrix\Main;
use Avito\Export\Assert;
use Avito\Export\Event;

/** @noinspection PhpUnused */
class FieldPublisher extends Event\Regular
{
	public static function getHandlers() : array
	{
		$result = [];

		foreach (static::getTypes() as $type)
		{
			$result[] = [
				'module' => 'main',
				'event' => 'OnUserTypeBuildList',
				'method' => 'getDescription',
				'arguments' => [ $type ],
				'sort' => 400,
			];
		}

		return $result;
	}

	public static function getDescription(string $type) : ?array
	{
		try
		{
			/** @var CharacteristicProperty $className */
			$className = __NAMESPACE__ . '\\' . $type . 'Field';

			Assert::classExists($className);
			Assert::methodExists($className, 'getUserTypeDescription');

			$result = $className::getUserTypeDescription();
		}
		catch (Main\SystemException $exception)
		{
			$result = null;
			trigger_error($exception->getMessage(), E_USER_WARNING);
		}

		return $result;
	}

	protected static function getTypes() : array
	{
		return [
			'Category',
		];
	}
}