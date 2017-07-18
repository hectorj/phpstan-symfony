<?php

declare(strict_types = 1);

namespace Lookyman\PHPStan\Symfony;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Lookyman\PHPStan\Symfony\ServiceMap
 */
final class ServiceMapTest extends TestCase
{

	public function testGetServices()
	{
		$serviceMap = new ServiceMap(__DIR__.'/container.xml');

		$servicesProperty = (new \ReflectionClass($serviceMap))->getProperty('services');
		$servicesProperty->setAccessible(true);

		self::assertEquals(
			[
				'withoutClass' => [
					'id' => 'withoutClass',
					'class' => \null,
					'public' => \true,
					'synthetic' => \false,
				],
				'withClass' => [
					'id' => 'withClass',
					'class' => 'Foo',
					'public' => \true,
					'synthetic' => \false,
				],
				'FullyQualified\Foo' => [
					'id' => 'FullyQualified\Foo',
					'class' => null,
					'public' => \true,
					'synthetic' => \false,
				],
				'withoutPublic' => [
					'id' => 'withoutPublic',
					'class' => 'Foo',
					'public' => \true,
					'synthetic' => \false,
				],
				'publicNotFalse' => [
					'id' => 'publicNotFalse',
					'class' => 'Foo',
					'public' => \true,
					'synthetic' => \false,
				],
				'private' => [
					'id' => 'private',
					'class' => 'Foo',
					'public' => \false,
					'synthetic' => \false,
				],
				'synthetic' => [
					'id' => 'synthetic',
					'class' => 'Foo',
					'public' => \true,
					'synthetic' => \true,
				],
				'alias' => [
					'id' => 'alias',
					'class' => 'Foo',
					'public' => \true,
					'synthetic' => \false,
				],
			],
			$servicesProperty->getValue($serviceMap)
		);
	}

}
