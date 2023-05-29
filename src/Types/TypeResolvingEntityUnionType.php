<?php

namespace Redeye\GraphqlFederationBundle\Types;

use GraphQL\Type\Definition\UnionType;
use Redeye\GraphqlFederationBundle\EntityTypeResolver\EntityTypeResolverInterface;

class TypeResolvingEntityUnionType extends UnionType
{
    /**
     * @param array|callable $entityTypes all entity types or a callable to retrieve them
     */
    public function __construct($entityTypes, EntityTypeResolverInterface $typeResolver)
    {
        $config = [
            'name' => self::getTypeName(),
            'types' => is_callable($entityTypes)
                ? fn () => array_values($entityTypes())
                : array_values($entityTypes),
            'resolveType' => $typeResolver,
        ];
        parent::__construct($config);
    }

    public static function getTypeName(): string
    {
        return '_Entity';
    }
}