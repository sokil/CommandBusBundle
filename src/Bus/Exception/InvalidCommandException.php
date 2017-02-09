<?php

namespace Sokil\CommandBusBundle\Bus\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidCommandException extends CommandBusException
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $list;

    /**
     * @param ConstraintViolationListInterface $list
     */
    public function setConstraintViolationList(ConstraintViolationListInterface $list)
    {
        $this->list = $list;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getConstraintViolationList()
    {
        return $this->list;
    }
}
