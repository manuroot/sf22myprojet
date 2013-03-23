<?php


namespace Application\PaypalBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Payum\Paypal\ExpressCheckout\Nvp\Bridge\Doctrine\Entity\PaymentInstruction;




use Payum\Paypal\ExpressCheckout\Nvp\PaymentInstruction;

class PaypalPaymentInstruction extends PaymentInstruction
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}