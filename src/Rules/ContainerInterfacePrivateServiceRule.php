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
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerInterfacePrivateServiceRule implements Rule
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
        if ($node instanceof MethodCall && $node->name === 'get' && $scope->getType($node->var)->getClass() === ContainerInterface::class) {
            $service = $this->serviceMap->getServiceFromNode($node->args[0] ?? null);

            if (!is_null($service) && !$service['public']) {
                return [\sprintf('Service "%s" is private.', $service['id'])];
            }
        }

        return [];
	}

}
