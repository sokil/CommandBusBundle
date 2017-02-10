<?php

namespace Sokil\CommandBusBundle\CommandBus;

interface CommandHandlerInterface
{
    /**
     * Handle command
     *
     * @param object $command
     * @return void
     */
    public function handle($command);

    /**
     * Check if command supported
     *
     * @param object $command
     * @return bool
     */
    public function supports($command);
}
