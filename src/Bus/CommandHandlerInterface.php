<?php

namespace Sokil\CommandBusBundle\Bus;

interface CommandHandlerInterface
{
    /**
     * @param object $command
     * @return mixed
     */
    public function handle($command);
}