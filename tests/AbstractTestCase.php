<?php

namespace Sokil\CommandBusBundle;

use Sokil\CommandBusBundle\DependencyInjection\RegisterCommandHandlerCompilerPass;
use Sokil\CommandBusBundle\Stub\CloseAccountCommand;
use Sokil\CommandBusBundle\Stub\CloseAccountCommandHandler;
use Sokil\CommandBusBundle\Stub\OpenAccountCommand;
use Sokil\CommandBusBundle\Stub\ProcessTransactionCommandHandler;
use Sokil\CommandBusBundle\Stub\SendMoneyCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    private function createAccountRepositoryDefinition()
    {
        $accountRepositoryServiceDefinition = new Definition(\SplFixedArray::class);
        $accountRepositoryServiceDefinition
            ->setFactory([\SplFixedArray::class, 'fromArray'])
            ->setArguments([
                [
                    0 => 10,
                    1 => 10,
                    2 => 10,
                    3 => 10,
                ]
            ]);

        return $accountRepositoryServiceDefinition;
    }

    private function createCloseAccountCommandHandlerDefinition()
    {
        $closeAccountCommandHandlerServiceDefinition = new Definition();
        $closeAccountCommandHandlerServiceDefinition
            ->setClass(CloseAccountCommandHandler::class)
            ->setArguments([new Reference('account_repository')])
            ->addTag(
                'sokil.command_handler',
                [
                    'command_class' => CloseAccountCommand::class,
                    'priority' => 100,
                ]
            );

        return $closeAccountCommandHandlerServiceDefinition;
    }

    private function createProcessTransactionCommandHandlerDefinition()
    {
        $processTransactionCommandHandlerServiceDefinition = new Definition();
        $processTransactionCommandHandlerServiceDefinition
            ->setClass(ProcessTransactionCommandHandler::class)
            ->setArguments([new Reference('account_repository')])
            ->addTag(
                'sokil.command_bus_handler',
                [
                    'command_class' => SendMoneyCommand::class,
                    'priority' => 200,
                ]
            );

        return $processTransactionCommandHandlerServiceDefinition;
    }

    private function createCommandHandlerWithNotSupportedCommandDefinition()
    {
        $processTransactionCommandHandlerServiceDefinition = new Definition();
        $processTransactionCommandHandlerServiceDefinition
            ->setClass(CloseAccountCommandHandler::class)
            ->setArguments([new Reference('account_repository')])
            ->addTag(
                'sokil.command_bus_handler',
                [
                    'command_class' => OpenAccountCommand::class,
                    'priority' => 200,
                ]
            );

        return $processTransactionCommandHandlerServiceDefinition;
    }

    /**
     * @return ContainerBuilder
     */
    protected function createContainer()
    {
        $containerBuilder = new ContainerBuilder();

        // load services
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__.'/../src/Resources/config'));
        $loader->load('services.yml');

        // account repository
        $containerBuilder->setDefinition('account_repository', $this->createAccountRepositoryDefinition());

        // close account handler definition
        $containerBuilder->setDefinition(
            'close_account_command_handler',
            $this->createCloseAccountCommandHandlerDefinition()
        );

        // process transaction handler definition
        $containerBuilder->setDefinition(
            'process_transaction_command_handler',
            $this->createProcessTransactionCommandHandlerDefinition()
        );

        // handler definition with not supported command
        $containerBuilder->setDefinition(
            'command_handler_with_unsupported_command',
            $this->createCommandHandlerWithNotSupportedCommandDefinition()
        );

        // process compiler pass
        $compilerPass = new RegisterCommandHandlerCompilerPass();
        $compilerPass->process($containerBuilder);

        // build container
        $containerBuilder->compile();

        return $containerBuilder;
    }
}