# rdey-graphql-federation-bundle

A tiny, simplistic and fragile bundle to integrate overblog/graphql-bundle 
(specifically 0.13 and 0.14) and skillshare/apollo-federation-php (1.7.0).

This bundle shouldn't have to exist, as this should more properly belong 
in overblog/graphql-bundle, but here we are.

## Usage

### Configuring types

You need to manually define your federated types as Symfony services. 
These services need to be tagged with `overblog_graphql.type`. Our
suggestion is this:

```yaml
    App\GraphQL\Type\:
        resource: '../src/GraphQL/Type'
        tags:
            - { name: 'overblog_graphql.type' }
```

Your type class can look a little something like this (see the docs for
skillshare/apollo-federation-php for more info):

```php
<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use Apollo\Federation\Types\EntityObjectType;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use GraphQL\Type\Definition\Type;

class TestEntity extends EntityObjectType implements AliasedInterface
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'TestEntity',
            'keyFields' => ['id', 'email'],
            'fields' => [
                'id' => [
                    'type' => Type::int(),
                ],
                'email' => [
                    'type' => Type::string(),
                ],
                'foobar' => [
                    'type' => Type::string(),
                ]
            ]
        ]);
    }

    public static function getAliases(): array
    {
        return ['TestEntity'];
    }
}
```

### Entity type resolution

The use case is analogous to TypeResolvers in OverblogGraphQLBundle. Implement `Redeye\GraphqlFederationBundle\EntityTypeResolver\EntityTypeResolverInterface` in a service, and tag it with `redeye_graphql_federation.entity_type_resolver`.

#### Example

```php
<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\Model\Person;
use GraphQL\Type\Definition\ResolveInfo;
use Redeye\GraphqlFederationBundle\EntityTypeResolver\EntityTypeResolverInterface;

class EntityTypeResolver implements EntityTypeResolverInterface
{
    public function __invoke($value, $context, ResolveInfo $info)
    {
        if ($value instanceof Person) {
            return 'Person';
        }

        return null;
    }
}

```

```yaml
services:
    App\GraphQL\EntityTypeResolver:
        tags:
            - { name: 'redeye_graphql_federation.entity_type_resolver' }
```

### Dumping the schema

You can dump a schema with the correct Federation directives using `bin/console redeye:graphql:dump-federated`.
