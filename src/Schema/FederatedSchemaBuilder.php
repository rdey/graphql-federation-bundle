<?php

declare(strict_types=1);

namespace Redeye\GraphqlFederationBundle\Schema;

use GraphQL\Type\Definition\Type;
use Overblog\GraphQLBundle\Definition\Builder\SchemaBuilder;
use Overblog\GraphQLBundle\Definition\Type\ExtensibleSchema;
use Overblog\GraphQLBundle\Resolver\TypeResolver;

class FederatedSchemaBuilder extends SchemaBuilder
{
    private TypeResolver $typeResolver;

    public function __construct(TypeResolver $typeResolver, bool $enableValidation = false)
    {
        $this->typeResolver = $typeResolver;
        $this->enableValidation = $enableValidation;
        parent::__construct($typeResolver, $enableValidation);
    }

    public function create(
        string $name,
        ?string $queryAlias,
        ?string $mutationAlias = null,
        ?string $subscriptionAlias = null,
        array $types = []
    ): ExtensibleSchema {
        $this->typeResolver->setCurrentSchemaName($name);
        $query = $this->typeResolver->resolve($queryAlias);
        $mutation = $this->typeResolver->resolve($mutationAlias);
        $subscription = $this->typeResolver->resolve($subscriptionAlias);

        if (!$query) {
            throw new \RuntimeException("Query is a required type");
        }

        $schema = new ExtensibleFederatedSchema($this->buildSchemaArguments($name, $query, $mutation, $subscription, $types));
        $extensions = [];

        $schema->setExtensions($extensions);

        return $schema;
    }

    private function buildSchemaArguments(string $schemaName, Type $query, ?Type $mutation, ?Type $subscription, array $types = []): array
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
        ];
    }
}
