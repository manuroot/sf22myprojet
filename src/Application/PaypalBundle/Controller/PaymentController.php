<?php

namespace Application\PaypalBundle\Controller;

use Application\PaypalBundle\Entity\PaypalPaymentInstruction as Controller;

class PaymentController extends Controller
{
    public function preparePaypalPaymentAction()
    {
        $contextName = 'simple_purchase_paypal_express_checkout';

        $paymentContext = $this->get('payum')->getContext($contextName);

        /** @var PaypalPaymentInstruction */
        $instruction = $paymentContext->getStorage()->createModel();
        $instruction->setPaymentrequestCurrencycode(0, 'USD');
        $instruction->setPaymentrequestAmt(0,  1.23);

        $paymentContext->getStorage()->updateModel($instruction);
        $instruction->setInvnum($instruction->getId());

        $returnUrl = $this->generateUrl('acme_payment_capture_simple', array(
            'contextName' => 'simple_purchase_paypal_express_checkout',
            'model' => $instruction->getId(),
        ), $absolute = true);
        $instruction->setReturnurl($returnUrl);
        $instruction->setCancelurl($returnUrl);

        $paymentContext->getStorage()->updateModel($instruction);

        return $this->forward('ApplicationPaypaltBundle:Capture:simpleCapture', array(
            'contextName' => $contextName,
            'model' => $instruction
        ));
    }
}