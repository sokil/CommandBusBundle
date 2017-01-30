<?php

namespace Sokil\CommandBusBundle;

use Sokil\CommandBusBundle\Bus\AbstractCommand;
use Sokil\CommandBusBundle\Bus\CommandHandlerServiceResolver;

class Bus
{
    /**
     * Map of command class name to handler service id relations
     * @var array
     */
    private $handlerDefinitions;

    /**
     * @var CommandHandlerServiceResolver
     */
    private $commandHandlerServiceResolver;

    /**
     * @param array $handlers
     * @param CommandHandlerServiceResolver $commandHandlerServiceResolver
     */
    public function __construct(
        array $handlers,
        CommandHandlerServiceResolver $commandHandlerServiceResolver
    ) {
        $this->handlerDefinitions = $handlers;
        $this->commandHandlerServiceResolver = $commandHandlerServiceResolver;
    }

    /**
     * @param AbstractCommand $command
     * @return mixed response
     */
    public function handle(AbstractCommand $command)
    {
        $commandClassName = get_class($command);

        // get handler definition by command instance
        if (empty($this->handlerDefinitions[$commandClassName])) {
            throw new \InvalidArgumentException('Passed command not configured in bus');
        }

        $handlerDefinition = $this->handlerDefinitions[$commandClassName];

        // get handler
        $handlerServiceId = $handlerDefinition['handler'];
        $handler = $this->commandHandlerServiceResolver->get($handlerServiceId);

        // execute command by handler
        return $handler->handle($command);
    }
}