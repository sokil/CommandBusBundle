<?php

namespace Sokil\CommandBusBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterCommandHandlerCompilerPass implements CompilerPassInterface
{
    const COMMAND_BUS_SERVICE_ID = 'sokil.command_bus';
    const TAG_NAME = 'sokil.command_bus_handler';
    const TAG_COMMAND_CLASS_ARGUMENT_NAME = 'command_class';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $handlers = [];

        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $commandBusHandlerServiceId => $commandBusHandlerTags) {
            foreach ($commandBusHandlerTags as $commandBusHandlerTagParams) {
                $commandClassName = $commandBusHandlerTagParams[self::TAG_COMMAND_CLASS_ARGUMENT_NAME];
                $handlers[$commandClassName] = [
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