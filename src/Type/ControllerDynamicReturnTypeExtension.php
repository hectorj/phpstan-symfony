<?php

declare(strict_types = 1);

namespace Lookyman\PHPStan\Symfony\Type;

use Lookyman\PHPStan\Symfony\ServiceMap;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ControllerDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	/**
	 * @var ServiceMap
	 */
	private $serviceMap;

	public function __construct(ServiceMap $symfonyServiceMap)
	{
		$this->serviceMap = $symfonyServiceMap;
	}

	public static function getClass(): string
	{
		return Controller::class;
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'get';
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type {
		$service = $this->serviceMap->getServiceFromNode($methodCall->args[0] ?? null);
		if (is_null($service) || $service['synthetic']) {
			return $methodReflection->getReturnType();
		}

		return new ObjectType($service['class'] ?? $service['id']);
	}

}
