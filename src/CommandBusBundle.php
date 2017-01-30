<?php

namespace Sokil\CommandBusBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Sokil\CommandBusBundle\DependencyInjection\RegisterCommandHandlerCompilerPass;

class CommandBusBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterCommandHandlerCompilerPass());
    }
}
