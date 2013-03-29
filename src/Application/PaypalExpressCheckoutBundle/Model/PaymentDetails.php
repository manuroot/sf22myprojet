<?php
namespace Application\PaypalExpressCheckoutBundle\Model;

//use Payum\Paypal\ExpressCheckout\Nvp\Bridge\Doctrine\Entity\PaymentInstruction as BasePaymentDetails;
use Payum\Paypal\ExpressCheckout\Nvp\Bridge\Doctrine\Entity\PaymentDetails as BasePaymentDetails;
class PaymentDetails extends BasePaymentDetails 
{
    protected $id;
    
    public function getId()
    {
        return $this->id;
    }
}