<?php

namespace Sokil\CommandBusBundle\Stub;

class OpenAccountCommand
{
    /**
     * @var int
     */
    private $accountId;

    /**
     * @var float
     */
    private $amount;

    /**
     * @param int $accountId
     */
    public function __construct($accountId, $amount)
    {
        $this->accountId = $accountId;
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
