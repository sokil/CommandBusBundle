<?php

namespace Sokil\CommandBusBundle;

use Sokil\CommandBusBundle\CommandBus\Exception\InvalidCommandException;
use Sokil\CommandBusBundle\Stub\OpenAccountCommand;
use Sokil\CommandBusBundle\Stub\SendMoneyCommand;
use Symfony\Component\Validator\ConstraintViolationList;

class CommandBusTest extends AbstractTestCase
{
    public function testHandle_SuccessfullyHandled()
    {
        $container = $this->createContainer();

        $sendMoneyCommand = new SendMoneyCommand(0, 1, 5);

        $container
            ->get('sokil.command_bus')
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
     * @expectedException \Sokil\CommandBusBundle\CommandBus\Exception\CommandUnacceptableByHandlerException
     */
    public function testHandle_WrongCommandPassedToHandler()
    {
        $container = $this->createContainer();

        $command = new OpenAccountCommand(42, 42);

        $container
            ->get('sokil.command_bus')
            ->handle($command);
    }

    /**
     * @expectedException \Sokil\CommandBusBundle\CommandBus\Exception\InvalidCommandException
     * @expectedExceptionMessage Command stdClass not configured in any handler
     */
    public function testHandle_UnconfiguredCommandPassedToHandler()
    {
        $container = $this->createContainer();

        $command = new \stdClass();

        $container
            ->get('sokil.command_bus')
            ->handle($command);
    }

    public function testHandle_InvalidCommand()
    {
        $constraintViolationList = new ConstraintViolationList();

        $exception = new InvalidCommandException();
        $exception->setConstraintViolationList($constraintViolationList);

        $this->assertSame($constraintViolationList, $exception->getConstraintViolationList());


    }
}