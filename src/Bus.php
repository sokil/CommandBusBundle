<?php

namespace Sokil\CommandBusBundle;

use Sokil\CommandBusBundle\Bus\CommandHandlerServiceResolver;
use Sokil\CommandBusBundle\Bus\Exception\CommandUnacceptableByHandlerException;

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
     * @param mixed $command
     * @return void
     */
    public function handle($command)
    {
        $commandClassName = get_class($command);

        // get handler definitions by command instance
        if (empty($this->handlerDefinitions[$commandClassName])) {
            throw new \InvalidArgumentException('Passed command not configured in bus');
        }

        $handlerDefinitions = $this->handlerDefinitions[$commandClassName];

        // order handlers by priority
        usort($handlerDefinitions, function($handlerDefinition1, $handlerDefinition2) {
            if ($handlerDefinition1['priority'] === $handlerDefinition2['priority']) {
                return 0;
            }

            return ($handlerDefinition1['priority'] < $handlerDefinition2['priority']) ? -1 : 1;
        });

        // handle
        foreach ($handlerDefinitions as $handlerDefinition) {
            // get handler
            $handlerServiceId = $handlerDefinition['handler'];
            $handler = $this->commandHandlerServiceResolver->get($handlerServiceId);

            // check if handler may handle command
            if (!$handler->supports($command)) {
                throw new CommandUnacceptableByHandlerException(sprintf(
                    'Command %s is not supported by handler %s',
                    get_class($handler),
                    get_class($command)
                ));
            }

            // execute command by handler
            $handler->handle($command);
        }
    }


}
