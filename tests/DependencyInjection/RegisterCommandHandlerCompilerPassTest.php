<?php

namespace Sokil\CommandBusBundle\DependencyInjection;

use Sokil\CommandBusBundle\Stub\CheckFraudTransactionCommandHandler;
use Sokil\CommandBusBundle\Stub\ProcessTransactionCommandHandler;
use Sokil\CommandBusBundle\Stub\SendMoneyCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class RegisterCommandHandlerCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return ContainerBuilder
     */
    private function createContainer()
    {
        $containerBuilder = new ContainerBuilder();

        // load services
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__.'/../../src/Resources/config'));
        $loader->load('services.yml');

        // fraud repository
        $fraudRepositoryServiceDefinition = new Definition(\SplFixedArray::class);
        $fraudRepositoryServiceDefinition
            ->setFactory([\SplFixedArray::class, 'fromArray'])
            ->setArguments([
                [
                    1 => false,
                    2 => false,
                    3 => true,
                    4 => true,
                ]
            ]);

        // account repository
        $accountRepositoryServiceDefinition = new Definition(\SplFixedArray::class);
        $accountRepositoryServiceDefinition
            ->setFactory([\SplFixedArray::class, 'fromArray'])
            ->setArguments([
                [
                    1 => 10,
                    2 => 10,
                    3 => 10,
                    4 => 10,
                ]
            ]);

        // first handler definition
        $checkFraudTransactionCommandHandlerServiceDefinition = new Definition();
        $checkFraudTransactionCommandHandlerServiceDefinition
            ->setClass(CheckFraudTransactionCommandHandler::class)
            ->setArguments([$fraudRepositoryServiceDefinition])
            ->addTag(
                RegisterCommandHandlerCompilerPass::TAG_NAME,
                [
                    'command_class' => SendMoneyCommand::class,
                    'priority' => 100,
                ]
            );

        $containerBuilder->setDefinition(
            'check_fraud_transaction_command_handler',
            $checkFraudTransactionCommandHandlerServiceDefinition
        );

        // second handler definition
        $processTransactionCommandHandlerServiceDefinition = new Definition();
        $processTransactionCommandHandlerServiceDefinition
            ->setClass(ProcessTransactionCommandHandler::class)
            ->setArguments([$accountRepositoryServiceDefinition])
            ->addTag(
                RegisterCommandHandlerCompilerPass::TAG_NAME,
                [
                    'command_class' => SendMoneyCommand::class,
                    'priority' => 200,
                ]
            );

        $containerBuilder->setDefinition(
            'process_transaction_command_handler',
            $processTransactionCommandHandlerServiceDefinition
        );

        // process compiler pass
        $compilerPass = new RegisterCommandHandlerCompilerPass();
        $compilerPass->process($containerBuilder);

        // build container
        $containerBuilder->compile();

        return $containerBuilder;
    }

    public function testProcess()
    {
        $container = $this->createContainer();

        $sendMoneyCommand = new SendMoneyCommand(1, 2, 5);

        $container
            ->get(RegisterCommandHandlerCompilerPass::COMMAND_BUS_SERVICE_ID)
            ->handle($sendMoneyCommand);

        // assert process log
        $this->assertSame(
            [
                'CheckFraudTransaction',
                'ProcessTransaction',
            ],
            $sendMoneyCommand->getLog()
        );
    }
}