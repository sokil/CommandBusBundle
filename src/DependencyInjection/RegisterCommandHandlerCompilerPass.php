<?php

namespace Sokil\CommandBusBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class RegisterCommandHandlerCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->registerHandlersInBuses($container);
    }

    /**
     * Get command buses
     *
     * @param ContainerBuilder $container
     * @return array
     */
    private function getBuses(ContainerBuilder $container)
    {
        $buses = [];
        $busDefinitions = $container->findTaggedServiceIds('sokil.command_bus');
        foreach ($busDefinitions as $busServiceId => $busTags) {
            foreach ($busTags as $busTagParams) {
                $buses[$busServiceId] = $container->findDefinition($busServiceId);
            }
        }

        return $buses;
    }

    /**
     * Add handlers to command buses
     *
     * @param ContainerBuilder $container
     */
    private function registerHandlersInBuses(ContainerBuilder $container)
    {
        // Get command buses
        $buses = $this->getBuses($container);

        // Add handlers to command buses
        $commandHandlerDefinitions = $container->findTaggedServiceIds('sokil.command_bus_handler');
        foreach ($commandHandlerDefinitions as $commandHandlerServiceId => $commandBusHandlerTags) {
            foreach ($commandBusHandlerTags as $commandHandlerTagParams) {
                // Get command class
                if (empty($commandHandlerTagParams['command_class'])) {
                    throw new InvalidArgumentException(sprintf(
                        "Parameter '%s' of command handler '%s' not specified",
                        'command_class',
                        $commandHandlerServiceId
                    ));
                }
                $commandClassName = $commandHandlerTagParams['command_class'];

                // Get handler's bus service id
                if (empty($commandHandlerTagParams['command_bus'])) {
                    $commandHandlersBusServiceId = 'sokil.command_bus.default';
                } else {
                    $commandHandlersBusServiceId = $commandHandlerTagParams['command_bus'];
                    if (empty($buses[$commandHandlersBusServiceId])) {
                        throw new InvalidArgumentException(sprintf(
                            "CommandBus with service id '%s' of command handler '%s' not found",
                            $commandHandlersBusServiceId,
                            $commandHandlerServiceId
                        ));
                    }
                }

                // Add handler definition to bus
                $buses[$commandHandlersBusServiceId]->addMethodCall(
                    'addHandlerDefinition',
                    [
                        $commandClassName,
                        $commandHandlerServiceId
                    ]
                );
            }
        }
    }
}
