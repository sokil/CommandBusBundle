<?php

namespace Sokil\CommandBusBundle\Stub;

use Sokil\CommandBusBundle\Bus\CommandHandlerInterface;
use Sokil\CommandBusBundle\Bus\Exception\InvalidCommandException;

class CheckFraudTransactionCommandHandler implements CommandHandlerInterface
{
    /**
     * @var \SplFixedArray
     */
    private $fraudRepository;

    /**
     * @param \SplFixedArray $fraudRepository
     */
    public function __construct(\SplFixedArray $fraudRepository)
    {
        $this->fraudRepository = $fraudRepository;
    }

    /**
     * @param object $command
     * @throws InvalidCommandException
     */
    public function handle($command)
    {
        /** @var $command SendMoneyCommand **/
        $command->log('CheckFraudTransaction');

        if (true === $this->fraudRepository[$command->getSenderId()]) {
            throw new InvalidCommandException('Sender is fraud');
        }

        if (true === $this->fraudRepository[$command->getRecipientId()]) {
            throw new InvalidCommandException('Recipient is fraud');
        }
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