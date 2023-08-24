<?php

declare(strict_types=1);

namespace Redeye\GraphqlFederationBundle\DependencyInjection\Compiler;

use Redeye\GraphqlFederationBundle\Schema\FederatedSchemaBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ReplaceSchemaBuilderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->setAlias('redeye_graphql.schema_builder', FederatedSchemaBuilder::class);
    }
}
