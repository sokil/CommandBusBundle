<?php

namespace Sokil\CommandBusBundle\Bus;

abstract class AbstractCommandHandler
{
    abstract public function handle(AbstractCommand $command);
}