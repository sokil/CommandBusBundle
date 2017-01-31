<?php

namespace Sokil\CommandBusBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterCommandHandlerCompilerPass implements CompilerPassInterface
{
    const COMMAND_BUS_SERVICE_ID = 'sokil.command_bus';
    const TAG_NAME = 'sokil.command_bus_handler';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $handlers = [];

        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $commandBusHandlerServiceId => $commandBusHandlerTags) {
            foreach ($commandBusHandlerTags as $commandBusHandlerTagParams) {
                $commandClassName = $commandBusHandlerTagParams['command_class'];
                $priority = !empty($commandBusHandlerTagParams['priority'])  ? (int)$commandBusHandlerTagParams['priority'] : 0;
                $handlers[$commandClassName][] = [
                    'priority' => $priority,
                    'handler' => $commandBusHandlerServiceId
                ];
            }
        }

        // set handler definitions to bus
        $container
            ->findDefinition(self::COMMAND_BUS_SERVICE_ID)
            ->replaceArgument(0, $handlers);
    }
}