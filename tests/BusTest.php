<?php

namespace Sokil\CommandBusBundle;

use Sokil\CommandBusBundle\DependencyInjection\RegisterCommandHandlerCompilerPass;
use Sokil\CommandBusBundle\Stub\CheckFraudTransactionCommandHandler;
use Sokil\CommandBusBundle\Stub\ProcessTransactionCommandHandler;
use Sokil\CommandBusBundle\Stub\SendMoneyCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class BusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return ContainerBuilder
     */
    private function createContainer()
    {
        $containerBuilder = new ContainerBuilder();

        // load services
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__.'/../src/Resources/config'));
        $loader->load('services.yml');

        // fraud repository
        $fraudRepositoryServiceDefinition = new Definition(\SplFixedArray::class);
        $fraudRepositoryServiceDefinition
            ->setFactory([\SplFixedArray::class, 'fromArray'])
            ->setArguments([
                [
                    0 => false,
                    1 => false,
                    2 => true,
                    3 => true,
                ]
            ]);

        $containerBuilder->setDefinition('fraud_repository', $fraudRepositoryServiceDefinition);

        // account repository
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

        $containerBuilder->setDefinition('account_repository', $accountRepositoryServiceDefinition);

        // first handler definition
        $checkFraudTransactionCommandHandlerServiceDefinition = new Definition();
        $checkFraudTransactionCommandHandlerServiceDefinition
            ->setClass(CheckFraudTransactionCommandHandler::class)
            ->setArguments([new Reference('fraud_repository')])
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
            ->setArguments([new Reference('account_repository')])
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

        $sendMoneyCommand = new SendMoneyCommand(0, 1, 5);

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

        // assert amount
        $this->assertSame(
            [
                0 => 5,
                1 => 15,
                2 => 10,
                3 => 10,
            ],
            $container->get('account_repository')->toArray()
        );
    }
}