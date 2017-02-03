<?php

namespace Sokil\CommandBusBundle\Stub;

use Sokil\CommandBusBundle\Bus\CommandHandlerInterface;
use Sokil\CommandBusBundle\Bus\Exception\InvalidCommandException;

class CloseAccountCommandHandler implements CommandHandlerInterface
{
    /**
     * @var \SplFixedArray
     */
    private $accountRepository;

    /**
     * @param \SplFixedArray $accountRepository
     */
    public function __construct(\SplFixedArray $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param CloseAccountCommand $command
     * @throws InvalidCommandException
     */
    public function handle($command)
    {
        if ($this->accountRepository->offsetExists($command->getAccountId())) {
            throw new InvalidCommandException('Invalid account passed');
        }

        $this->accountRepository[$command->getAccountId()] = null;
    }

    /**
     * @param object $command
     * @return bool
     */
    public function supports($command)
    {
        return $command instanceof CloseAccountCommand;
    }
}