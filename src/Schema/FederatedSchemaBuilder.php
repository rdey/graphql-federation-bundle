<?php

declare(strict_types=1);

namespace Redeye\GraphqlFederationBundle\Schema;

use Apollo\Federation\Types\EntityObjectType;
use GraphQL\Type\Definition\Type;
use Overblog\GraphQLBundle\Definition\Builder\SchemaBuilder;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\ExtensibleSchema;
use Overblog\GraphQLBundle\Resolver\TypeResolver;
use Redeye\GraphqlFederationBundle\EntityTypeResolver\EntityTypeResolverInterface;

class FederatedSchemaBuilder extends SchemaBuilder
{
    private TypeResolver $typeResolver;
    private EntityTypeResolverInterface $entityTypeResolver;

    public function __construct(TypeResolver $typeResolver, EntityTypeResolverInterface $entityTypeResolver)
    {
        $this->typeResolver = $typeResolver;
        $this->entityTypeResolver = $entityTypeResolver;
        parent::__construct($typeResolver, false);
    }

    public function create(
        string $name,
        ?string $queryAlias,
        ?string $mutationAlias = null,
        ?string $subscriptionAlias = null,
        array $types = []
    ): ExtensibleSchema {
        $entityTypes = [];

        foreach ($this->typeResolver->getSolutions() as $type) {
            if ($type instanceof EntityObjectType) {
                $entityTypes[$type->name] = $type;
            }
        }

        $this->typeResolver->setCurrentSchemaName($name);
        $query = $this->typeResolver->resolve($queryAlias);
        $mutation = $this->typeResolver->resolve($mutationAlias);
        $subscription = $this->typeResolver->resolve($subscriptionAlias);

        if (!$query) {
            throw new \RuntimeException("Query is a required type");
        }

        $schema = new ExtensibleFederatedSchema($this->buildSchemaArguments($name, $query, $mutation, $subscription, $types, $entityTypes), $this->entityTypeResolver);
        $extensions = [];

        $schema->setExtensions($extensions);

        return $schema;
    }

    private function buildSchemaArguments(string $schemaName, Type $query, ?Type $mutation, ?Type $subscription, array $types = [], array $entityTypes = []): array
    {
        return [
            'query' => $query,
            'mutation' => $mutation,
            'subscription' => $subscription,
            'typeLoader' => function ($name) use ($schemaName) {
                $this->typeResolver->setCurrentSchemaName($schemaName);

                return $this->typeResolver->resolve($name);
            },
            'types' => function () use ($types, $schemaName) {
                $this->typeResolver->setCurrentSchemaName($schemaName);

                return array_map([$this->typeResolver, 'resolve'], $types);
            },
            'entityTypes' => function() use ($entityTypes) {
                return $entityTypes;
            },
        ];
    }
}
