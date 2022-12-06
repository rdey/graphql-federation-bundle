<?php

declare(strict_types=1);

namespace Redeye\GraphqlFederationBundle\Command;

use Apollo\Federation\Utils\FederatedSchemaPrinter;
use Overblog\GraphQLBundle\Request\Executor as RequestExecutor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpFederatedSchemaCommand extends Command
{
    private RequestExecutor $requestExecutor;

    public function __construct(RequestExecutor $requestExecutor)
    {
        parent::__construct("redeye:graphql:dump-federated");
        $this->requestExecutor = $requestExecutor;
    }

    public function getRequestExecutor(): RequestExecutor
    {
        return $this->requestExecutor;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $content = FederatedSchemaPrinter::doPrint($this->getRequestExecutor()->getSchema('default'));

        $output->write($content);

        return Command::SUCCESS;
    }
}
