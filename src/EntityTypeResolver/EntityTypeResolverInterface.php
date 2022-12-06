<?php

declare(strict_types=1);

namespace Redeye\GraphqlFederationBundle\EntityTypeResolver;

use GraphQL\Type\Definition\ResolveInfo;

interface EntityTypeResolverInterface
{
    public function __invoke($value, $context, ResolveInfo $info);
}
