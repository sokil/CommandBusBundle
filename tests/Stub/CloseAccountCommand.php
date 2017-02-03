<?php

namespace Sokil\CommandBusBundle\Stub;

class CloseAccountCommand
{
    /**
     * @var int
     */
    private $accountId;

    /**
     * @param int $accountId
     */
    public function __construct($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }
}