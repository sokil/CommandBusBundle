<?php

namespace Sokil\CommandBusBundle;

class RegisterCommandHandlerCompilerPassTest extends AbstractTestCase
{
    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @expectedExceptionMessage Parameter 'command_class' of command handler 'close_account_command_handler' not specified
     */
    public function testRegisterHandlersInBuses()
    {
        $this->createBrokenContainerWithNotPassedHandlersCommandClass();
    }
}