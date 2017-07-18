<?php

declare(strict_types = 1);

namespace Lookyman\PHPStan\Symfony\Rules;

use Lookyman\PHPStan\Symfony\ServiceMap;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerInterfaceUnknownServiceRule implements Rule
{

	/**
	 * @var ServiceMap
	 */
	private $serviceMap;

	public function __construct(ServiceMap $symfonyServiceMap)
	{
		$this->serviceMap = $symfonyServiceMap;
	}

	public function getNodeType(): string
	{
		return MethodCall::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if ($node instanceof MethodCall && $node->name === 'get' && in_array($scope->getType($node->var)->getClass(), [ContainerInterface::class, Controller::class])) {
			$service = $this->serviceMap->getServiceFromNode($node->args[0] ?? null);

			if (is_null($service)) {
				return [\sprintf('Service "%s" is not registered in the container.', $node->args[0]->value->value ?? $node->args[0]->value->class)];
			}
		}

		return [];
	}

}
