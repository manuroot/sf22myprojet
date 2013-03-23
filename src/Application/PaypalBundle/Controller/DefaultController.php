<?php

namespace Application\PaypalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ApplicationPaypalBundle:Default:index.html.twig', array('name' => $name));
    }
}
