<?php
namespace Application\PaymentBundle\Model;

class OmnipayInstruction extends \ArrayObject
{
    protected $id;
    
    public function getId()
    {
        return $this->id;
    }
}