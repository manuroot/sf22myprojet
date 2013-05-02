<?php

namespace Application\RelationsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ApplicationRelationsBundle:Default:index.html.twig', array('name' => $name));
    }
}
