<?php

namespace Application\PaypalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Paypal\ExpressCheckout\Nvp\Bridge\Doctrine\Entity\PaymentInstruction;
//use Payum\Paypal\ExpressCheckout\Nvp\Bridge\Doctrine\Entity\PaymentDetails as BasePaymentDetails;
/**
 * @ORM\Entity
 */
class PaypalPaymentInstruction extends PaymentInstruction
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
}
