<?php

namespace Application\JMSPaymentBundle\Controller;


use JMS\DiExtraBundle\Annotation as DI;
use JMS\Payment\CoreBundle\Entity\Payment;
use JMS\Payment\CoreBundle\PluginController\Result;
use JMS\Payment\CoreBundle\Plugin\Exception\ActionRequiredException;
use JMS\Payment\CoreBundle\Plugin\Exception\Action\VisitUrl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PaiementController extends Controller
{
    /** @DI\Inject */
    private $request;
 
    /** @DI\Inject */
    private $router;
 
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $em;
 
    /** @DI\Inject("payment.plugin_controller") */
    private $ppc;
 
    /**
     * @Route("/{id}", name="paiement")
     * @Template()
     */
    public function paymentAction($id=0) // this is a personnal ID i pass to the controler to identify the previous shopping cart
    {
        $form = $this->getFormFactory()->create('jms_choose_payment_method', null, array(
            'amount'   => $order->getPrice(),
            'currency' => 'EUR',
            'default_method' => 'payment_paypal', // Optional
            'predefined_data' => array()
        ));
 
        if ('POST' === $this->request->getMethod()) {
            $form->bindRequest($this->request);
            $order = new Order();
            $this->em->persist( $order);
            $this->em->flush( $order);
 
            $form = $this->getFormFactory()->create('jms_choose_payment_method', null, array(
                'amount'   => $order->getPrice(),
                'currency' => 'EUR',
                'default_method' => 'payment_paypal', // Optional
                'predefined_data' => array(
                    'paypal_express_checkout' => array(
                        'return_url' => $this->router->generate('payment_complete', array(
                            'id' =>$order->getId()
                        ), true),
                        'cancel_url' => $this->router->generate('payment_cancel', array(
                            'id' => $order->getId()
                        ), true)
                    ),
                ),
            ));
 
            $form->bindRequest($this->request);
 
    // Once the Form is validate, you update the order with payment instruction
            if ($form->isValid()) {
                $instruction = $form->getData();
                $this->ppc->createPaymentInstruction($instruction);
                $order->setPaymentInstruction($instruction);
                $this->em->persist($order);
                $this->em->flush($order);
                // now, let's redirect to payment_complete with the order id
                return new RedirectResponse($this->router->generate('payment_complete', array('id' => $order->getId() )));
            }
        }
        return $this->render('ApplicationJMSPaymentBundle:Paiement:paymentChooseTemplate.html.twig',array('form' => $form->createView() , 'id' => $id));
    }
    
     /**
     * @Route("/complete/{id}", name = "payment_complete")
     */
    public function completeAction($id=0) // id of the order
    {
        if ( $id != 0 ){
            $order = $this->getDoctrine()->getRepository("ApplicationJMSPaymentBundle:order")->find($id);}
 
        $instruction = $order->getPaymentInstruction();
        if (null === $pendingTransaction = $instruction->getPendingTransaction()) {
            $payment = $this->ppc->createPayment($instruction->getId(), $instruction->getAmount() - $instruction->getDepositedAmount());
        } else {
            $payment = $pendingTransaction->getPayment();
        }
 
        $result = $this->ppc->approveAndDeposit($payment->getId(), $payment->getTargetAmount());
        if (Result::STATUS_PENDING === $result->getStatus()) {
            $ex = $result->getPluginException();
            if ($ex instanceof ActionRequiredException) {
                $action = $ex->getAction();
                if ($action instanceof VisitUrl) {
                    return new RedirectResponse($action->getUrl());
                }
                throw $ex;
            }
        } else if (Result::STATUS_SUCCESS !== $result->getStatus()) {
            throw new \RuntimeException('Transaction was not successful: '.$result->getReasonCode());
        }
        // if successfull, i render my order validation template
        return $this->render('ApplicationJMSPaymentBundle:Paiement:validation.html.twig',array('order'=>$order ));
 
    }
 
    /** @DI\LookupMethod("form.factory") */
    protected function getFormFactory() { }
 
    /**
     * @Route("/cancel", name = "payment_cancel")
     */
    public function CancelAction( )
    {
        $this->get('session')->getFlashBag()->add('info', 'Transaction annulée.');
        return $this->redirect($this->generateUrl('yourTemplate'));
    }
}
