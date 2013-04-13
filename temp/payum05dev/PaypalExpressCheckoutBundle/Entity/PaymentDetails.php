<?php
namespace Application\PaypalExpressCheckoutBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Payum\Paypal\ExpressCheckout\Nvp\Bridge\Doctrine\Entity\PaymentDetails as BasePaymentDetails;

/**
 * @ORM\Entity
 */
class PaymentDetails extends BasePaymentDetails
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
}