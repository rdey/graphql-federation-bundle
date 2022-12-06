<?php

declare(strict_types=1);

namespace Redeye\GraphqlFederationBundle\EntityTypeResolver;

use GraphQL\Type\Definition\ResolveInfo;

class ChainEntityTypeResolver implements EntityTypeResolverInterface
{
    private iterable $entityTypeResolvers;

    /**
     * @param iterable<EntityTypeResolverInterface> $entityTypeResolvers
     */
    public function __construct(iterable $entityTypeResolvers = [])
    {
        $this->entityTypeResolvers = $entityTypeResolvers;
    }

    public function __invoke($value, $context, ResolveInfo $info)
    {
        foreach ($this->entityTypeResolvers as $typeResolver) {
            $type = $typeResolver($value, $context, $info);
            if ($type) {
                return $type;
            }
        }

        return null;
    }
}
