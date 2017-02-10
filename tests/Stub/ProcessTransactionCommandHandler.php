<?php

namespace Sokil\CommandBusBundle\Stub;

use Sokil\CommandBusBundle\CommandBus\CommandHandlerInterface;
use Sokil\CommandBusBundle\CommandBus\Exception\InvalidCommandException;

class ProcessTransactionCommandHandler implements CommandHandlerInterface
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
     * @param object $command
     * @throws InvalidCommandException
     */
    public function handle($command)
    {
        if (!isset($this->accountRepository[$command->getSenderId()])) {
            throw new InvalidCommandException('Sender has no active account');
        }

        if (!isset($this->accountRepository[$command->getRecipientId()])) {
            throw new InvalidCommandException('Recipient has no active account');
        }

        if ($command->getSenderId() === $command->getRecipientId()) {
            throw new InvalidCommandException('Sender can\'t send money to himself');
        }

        if ($this->accountRepository[$command->getSenderId()] < $command->getAmount()) {
            throw new InvalidCommandException('Sender has not enough money');
        }

        $this->accountRepository[$command->getSenderId()] -= $command->getAmount();
        $this->accountRepository[$command->getRecipientId()] += $command->getAmount();
    }

    /**
     * @param object $command
     * @return bool
     */
    public function supports($command)
    {
        return $command instanceof SendMoneyCommand;
    }
}