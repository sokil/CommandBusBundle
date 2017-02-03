<?php

namespace Sokil\CommandBusBundle;

use Sokil\CommandBusBundle\Bus\Exception\CommandUnacceptableByHandlerException;
use Sokil\CommandBusBundle\DependencyInjection\RegisterCommandHandlerCompilerPass;
use Sokil\CommandBusBundle\Stub\OpenAccountCommand;
use Sokil\CommandBusBundle\Stub\SendMoneyCommand;

class BusTest extends AbstractTestCase
{


    public function testHandle_SuccessfullyHandled()
    {
        $container = $this->createContainer();

        $sendMoneyCommand = new SendMoneyCommand(0, 1, 5);

        $container
            ->get(RegisterCommandHandlerCompilerPass::COMMAND_BUS_SERVICE_ID)
            ->handle($sendMoneyCommand);

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

    /**
     * @expectedException \Sokil\CommandBusBundle\Bus\Exception\CommandUnacceptableByHandlerException
     */
    public function testHandle_WrongCommandPassedToHandler()
    {
        $container = $this->createContainer();

        $command = new OpenAccountCommand(42, 42);

        $container
            ->get(RegisterCommandHandlerCompilerPass::COMMAND_BUS_SERVICE_ID)
            ->handle($command);
    }
}