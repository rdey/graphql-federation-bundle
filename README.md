# rdey-graphql-federation-bundle

A tiny, simplistic and fragile bundle to integrate overblog/graphql-bundle 
(specifically 0.13 and 0.14) and skillshare/apollo-federation-php (1.6).

This bundle shouldn't have to exist, as this should more properly belong 
in overblog/graphql-bundle, but here we are.

## Usage

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

You can also dump a schema with the correct Federation directives using `bin/console redeye:graphql:dump-federated`.
