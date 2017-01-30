<?php

namespace Sokil\CommandBusBundle\Bus;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CommandHandlerServiceResolver implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Get command handler
     * @param string $handlerServiceId
     * @return AbstractCommandHandler
     */
    public function get($handlerServiceId)
    {
        $handler = $this->container->get($handlerServiceId);

        if (!$handler instanceof AbstractCommandHandler) {
            throw new \InvalidArgumentException('Handler must be instance if ' . AbstractCommandHandler::class);
        }

        return $handler;
    }
}