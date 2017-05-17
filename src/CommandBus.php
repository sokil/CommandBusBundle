<?php

namespace Sokil\CommandBusBundle;

use Sokil\CommandBusBundle\CommandBus\CommandHandlerServiceResolver;
use Sokil\CommandBusBundle\CommandBus\Exception\CommandUnacceptableByHandlerException;
use Sokil\CommandBusBundle\CommandBus\Exception\InvalidCommandException;

class CommandBus
{
    /**
     * Map of command class name to handler service id relations
     * @var array
     */
    private $handlerDefinitions = [];

    /**
     * @var CommandHandlerServiceResolver
     */
    private $commandHandlerServiceResolver;

    /**
     * @param CommandHandlerServiceResolver $commandHandlerServiceResolver
     */
    public function __construct(
        CommandHandlerServiceResolver $commandHandlerServiceResolver
    ) {
        $this->commandHandlerServiceResolver = $commandHandlerServiceResolver;
    }

    /**
     * Add handler definition
     * @param string $commandClassName
     * @param string $handlerServiceId
     * @return CommandBus
     */
    public function addHandlerDefinition($commandClassName, $handlerServiceId)
    {
        // Check if handler already associated with command
        if (isset($this->handlerDefinitions[$commandClassName])) {
            throw new \InvalidArgumentException(sprintf(
                "Handler with service id '%s' already configured for command %s'",
                $this->handlerDefinitions[$commandClassName]['handler'],
                $commandClassName
            ));
        }

        // Add handler definition
        $this->handlerDefinitions[$commandClassName] = [
            'handler' => $handlerServiceId,
        ];

        return $this;
    }

    /**
     * @param mixed $command
     * @return mixed
     * @throws CommandUnacceptableByHandlerException
     */
    public function handle($command)
    {
        $commandClassName = get_class($command);

        // get handler definitions by command instance
        if (empty($this->handlerDefinitions[$commandClassName])) {
            throw new InvalidCommandException(sprintf(
                'Command %s not configured in any handler',
                $commandClassName
            ));
        }

        $handlerDefinition = $this->handlerDefinitions[$commandClassName];

        // get handler
        $handlerServiceId = $handlerDefinition['handler'];
        $handler = $this->commandHandlerServiceResolver->get($handlerServiceId);

        // check if handler may handle command
        if (!$handler->supports($command)) {
            throw new CommandUnacceptableByHandlerException(sprintf(
                'Command %s is not supported by handler %s',
                get_class($command),
                get_class($handler)
            ));
        }

        // execute command by handler
        return $handler->handle($command);
    }
}
