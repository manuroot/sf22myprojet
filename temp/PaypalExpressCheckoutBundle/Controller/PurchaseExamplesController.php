<?php

namespace Application\PaypalExpressCheckoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Range;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Payum\Bundle\PayumBundle\Context\ContextRegistry;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Application\PaypalExpressCheckoutBundle\Model\PaymentDetails;

class PurchaseExamplesController extends Controller {

    /**
     * @Extra\Route(
     *   "/prepare_simple_purchase", 
     *   name="application_paypal_express_checkout_prepare_simple_purchase"
     * )
     * 
     * @Extra\Template
     */
    public function prepareAction(Request $request) {
        $form = $this->createPurchaseForm();
        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if (!isset($data['currency']))
                    $data['currency'] = "EUR";

                $paymentContext = $this->getPayum()->getContext('simple_purchase_paypal_express_checkout');

                /** @var $paymentDetails PaymentDetails */
                $paymentDetails = $paymentContext->getStorage()->createModel();
                $paymentDetails->setPaymentrequestCurrencycode(0, $data['currency']);
                $paymentDetails->setPaymentrequestAmt(0, $data['amount']);

                $paymentContext->getStorage()->updateModel($paymentDetails);
                $paymentDetails->setInvnum($paymentDetails->getId());

                $captureUrl = $this->generateUrl('application_payment_capture_simple', array(
                    'contextName' => 'simple_purchase_paypal_express_checkout',
                    'model' => $paymentDetails->getId(),
                        ), $absolute = true);
                $paymentDetails->setReturnurl($captureUrl);
                $paymentDetails->setCancelurl($captureUrl);

                $paymentContext->getStorage()->updateModel($paymentDetails);

                return $this->redirect($captureUrl);
            }
        }

        return array(
            'form_paypal' => $form->createView()
        );
    }

    /**
     * @Extra\Route(
     *   "/prepare_simple_purchase_and_doctrine",
     *   name="application_paypal_express_checkout_prepare_simple_purchase_and_doctrine"
     * )
     * 
     * @Extra\Template
     */
    public function prepareDoctrineAction(Request $request) {
        $form = $this->createPurchaseForm();
        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                //   print_r($data);
                //   exit(1);
                if (!isset($data['currency']))
                    $data['currency'] = "EUR";
                $paymentContext = $this->getPayum()->getContext('simple_purchase_paypal_express_checkout_doctrine');

                /** @var $paymentDetails PaymentDetails */
                $paymentDetails = $paymentContext->getStorage()->createModel();
                $paymentDetails->setPaymentrequestCurrencycode(0, $data['currency']);
                $paymentDetails->setPaymentrequestAmt(0, $data['amount']);

                $paymentDetails->setLPaymentrequestName(0, 0, 'Participation MROOT BLOG');
                $paymentDetails->setLPaymentrequestDesc(0, 0, 'Une aide au developpement');
                $paymentDetails->setLPaymentrequestAmt(0, 0, $data['amount']);
                $paymentDetails->setLPaymentrequestQty(0, 0, 1);

                $paymentContext->getStorage()->updateModel($paymentDetails);
                $paymentDetails->setInvnum($paymentDetails->getId());

                $captureUrl = $this->generateUrl('application_payment_capture_simple', array(
                    'contextName' => 'simple_purchase_paypal_express_checkout_doctrine',
                    'model' => $paymentDetails->getId(),
                        ), $absolute = true);
                $paymentDetails->setReturnurl($captureUrl);
                $paymentDetails->setCancelurl($captureUrl);

                $paymentContext->getStorage()->updateModel($paymentDetails);

                //we do forward since we do not store returnulr to database.
                return $this->forward('ApplicationPaymentBundle:Capture:simpleCapture', array(
                            'contextName' => 'simple_purchase_paypal_express_checkout_doctrine',
                            'model' => $paymentDetails
                        ));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Extra\Route(
     *   "/digital_goods_purchase",
     *   name="application_paypal_express_checkout_prepare_digital_goods_purchase"
     * )
     * 
     * @Extra\Template
     */
    public function prepareDigitalGoodsAction(Request $request) {
        $eBook = array(
            'author' => 'Jules Verne',
            'name' => 'The Mysterious Island',
            'description' => 'The Mysterious Island is a novel by Jules Verne, published in 1874.',
            'price' => 2.64,
            'currency_symbol' => '$',
            'currency' => 'USD',
            'quantity' => 2
        );

        if ('POST' === $request->getMethod()) {
            $paymentContext = $this->getPayum()->getContext('simple_purchase_paypal_express_checkout');

            /** @var $paymentDetails PaymentDetails */
            $paymentDetails = $paymentContext->getStorage()->createModel();
            $paymentDetails->setPaymentrequestCurrencycode(0, $eBook['currency']);
            $paymentDetails->setPaymentrequestAmt(0, $eBook['price'] * $eBook['quantity']);

            $paymentDetails->setNoshipping(Api::NOSHIPPING_NOT_DISPLAY_ADDRESS);
            $paymentDetails->setReqconfirmshipping(Api::REQCONFIRMSHIPPING_NOT_REQUIRED);
            $paymentDetails->setLPaymentrequestItemcategory(0, 0, Api::PAYMENTREQUEST_ITERMCATEGORY_DIGITAL);
            $paymentDetails->setLPaymentrequestAmt(0, 0, $eBook['price']);
            $paymentDetails->setLPaymentrequestQty(0, 0, $eBook['quantity']);
            $paymentDetails->setLPaymentrequestName(0, 0, $eBook['author'] . '. ' . $eBook['name']);
            $paymentDetails->setLPaymentrequestDesc(0, 0, $eBook['description']);

            $paymentContext->getStorage()->updateModel($paymentDetails);
            $paymentDetails->setInvnum($paymentDetails->getId());

            $captureUrl = $this->generateUrl('application_payment_capture_simple', array(
                'contextName' => 'simple_purchase_paypal_express_checkout',
                'model' => $paymentDetails->getId(),
                    ), $absolute = true);
            $paymentDetails->setReturnurl($captureUrl);
            $paymentDetails->setCancelurl($captureUrl);

            $paymentContext->getStorage()->updateModel($paymentDetails);

            return $this->redirect($captureUrl);
        }

        return array(
            'book' => $eBook
        );
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function createPurchaseForm() {



        return $this->createFormBuilder()
                        ->add('amount', 'choice', array(
                            'label' => 'Montant',
                            'choices' => array(
                                1 => 1,
                                2 => 2,
                                10 => 10, 20 => 20, 50 => 50, 100 => 100, 200 => 200),
                            'preferred_choices' => array(10),
                        ))
                        /* ->add('amount', 'choice', 
                          array(
                          'data' => array('1' => '1', '10' => '10'),)) */
                        /*  ->add('amount', null, array(
                          'data' => 1,
                          'constraints' => array(new Range(array('max' => 100)))
                          )) */
                        ->add('currency', null, array('data' => 'EUR', 'label' => 'Devise'))
                        //<input type="hidden" name="item_name" value="Your Description Here"> 
                        ->add('item_name', 'hidden', array(
                            'data' => 'Participation Au Blog MROOT',
                        ))

                        // ->add('currency', null, array('data' => 'EUR'))
                        ->getForm()
        ;
    }

    /**
     * @return ContextRegistry
     */
    protected function getPayum() {
        return $this->get('payum');
    }

}