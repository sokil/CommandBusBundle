<?php

namespace Sokil\CommandBusBundle\Stub;

class SendMoneyCommand
{
    /**
     * @var int
     */
    private $senderId;

    /**
     * @var int
     */
    private $recipientId;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var array
     */
    private $transactionLog = [];

    /**
     * @param int $senderId
     * @param int $recipientId
     * @param float $amount
     */
    public function __construct($senderId, $recipientId, $amount)
    {
        $this->senderId = $senderId;
        $this->recipientId = $recipientId;
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * @return int
     */
    public function getRecipientId()
    {
        return $this->recipientId;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Log process of passing command through handlers
     */
    public function log($message)
    {
        $this->transactionLog[] = $message;
    }

    /**
     * @return array
     */
    public function getLog()
    {
        return $this->transactionLog;
    }
}