<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\NewsBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\NewsBundle\Model\PostInterface;
use Doctrine\ORM\EntityRepository;
use Sonata\NewsBundle\Controller\PostController as Controller;
class PostController extends Controller {

    /**
     * @return RedirectResponse
     */
    public function homeAction() {
        return $this->redirect($this->generateUrl('sonata_news_archive'));
    }

    private function sidebar_tags() {

        $em = $this->container->get('doctrine')->getEntityManager();
        $alltags = $em->getRepository('ApplicationSonataNewsBundle:Tag')->findByEnabled(1);
             //   ->findAll();
      return ($alltags);
    }

    private function sidebar_categories() {
        $em = $this->container->get('doctrine')->getEntityManager();
        $allcategories = $em->getRepository('ApplicationSonataNewsBundle:Category')->findByEnabled(1);
            //    ->findAll();
       return ($allcategories);
    }

    /**
     * @param array $criteria
     *
     * @return Response
     */
    public function renderArchive(array $criteria = array(), array $parameters = array()) {
        
        
     /*   print_r($criteria);
        exit(1);
       */
//  $this->setMaxPerPage(2);
        $form_paypal = $this->createPurchaseForm();
        $pager = $this->getPostManager()->getPager(
                $criteria, $this->getRequest()->get('page', 1),2
        );
        $test = "(surcharge du controleur: phase de développement)";
          $alltags = $this->sidebar_tags();
          $allcategories = $this->sidebar_categories();
        // $alltags=$em->getRepository('ApplicationSonataNewsBundle:Tag')->findAll();
         $parameters = array_merge(array(
          'pager' => $pager,
          'blog'  => $this->get('sonata.news.blog'),
          'tag'   => false,
          'test'=> $test,
          'form_paypal' => $form_paypal->createView(),
          'allcategories' => $allcategories,
          'alltags' => $alltags,
          ), $parameters); 
        $response = $this->render(sprintf('SonataNewsBundle:Post:archive.%s.twig', $this->getRequest()->getRequestFormat()), $parameters);

        if ('rss' === $this->getRequest()->getRequestFormat()) {
            $response->headers->set('Content-Type', 'application/rss+xml');
        }

        return $response;
    }

    
    
      public function mesnewsAction(array $criteria = array(), array $parameters = array()) {
        
        $form_paypal = $this->createPurchaseForm();
       /*  $mypager = $this->getPostManager()-> getPager(
                $criteria, $this->getRequest()->get('page', 1)
        );*/
         //$mypager->getResult();
        /*$pager = $this->getPostManager()-> getPagerquery(
                $criteria, $this->getRequest()->get('page', 1)
        );*/
         $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('ApplicationSonataNewsBundle:Post')->myFindAll();
          $alltags = $this->sidebar_tags();
          $allcategories = $this->sidebar_categories();
      
            $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $this->get('request')->query->get('page', 1)/* page number */, 10/* limit per page */
        );
   
     $pagination->setTemplate('ApplicationSonataNewsBundle:pagination:twitter_bootstrap_pagination.html.twig');
     
         return $this->render('ApplicationSonataNewsBundle:Post:mesnews.html.twig',
                array(
                   // 'pager' => $mypager,
                'pagination' => $pagination,
                       'form_paypal' => $form_paypal->createView(),
                       'allcategories' => $allcategories,
          'alltags' => $alltags,
      
      ));
   
    }

  

    /**
     * @throws NotFoundHttpException
     *
     * @param $permalink
     *
     * @return Response
     */
    public function viewAction($permalink) {
        $form_paypal = $this->createPurchaseForm();
        $post = $this->getPostManager()->findOneByPermalink($permalink, $this->container->get('sonata.news.blog'));

        if (!$post || !$post->isPublic()) {
            throw new NotFoundHttpException('Unable to find the post');
        }

        if ($seoPage = $this->getSeoPage()) {
            $seoPage
                    ->setTitle($post->getTitle())
                    ->addMeta('name', 'description', $post->getAbstract())
                    ->addMeta('property', 'og:title', $post->getTitle())
                    ->addMeta('property', 'og:type', 'blog')
                    ->addMeta('property', 'og:url', $this->generateUrl('sonata_news_view', array(
                                'permalink' => $this->getBlog()->getPermalinkGenerator()->generate($post, true)
                                    ), true))
                    ->addMeta('property', 'og:description', $post->getAbstract())
            ;
        }

        return $this->render('SonataNewsBundle:Post:view.html.twig', array(
                    'post' => $post,
                    'form' => false,
                    'blog' => $this->get('sonata.news.blog'),
                    'form_paypal' => $form_paypal->createView(),
                ));
    }

     /**
     * @param $postId
     * @param bool $form
     *
     * @return Response
     */
    public function addCommentFormAction($postId, $form = false) {
    //    parent::addCommentFormAction($postId, $form=false);
        if (!$form) {
            $post = $this->getPostManager()->findOneBy(array(
                'id' => $postId
                    ));
       /*$em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $user_security = $this->container->get('security.context');
        //if( $user_security->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
      if ($user_security->isGranted('IS_AUTHENTICATED_FULLY')) {
             $user_id = $user->getId();
        
              $current_user = $em->getRepository('ApplicationSonataUserBundle:User')->find($user_id);
        $post->setDemandeur($current_user);

             
        }*/
            $form = $this->getCommentForm($post);
            $form->setData(array('email'=>'email'));

        }

        return $this->render('SonataNewsBundle:Post:comment_form.html.twig', array(
                    'form' => $form->createView(),
                    'post_id' => $postId
                ));
    }

   
  
    protected function createPurchaseForm() {

        return $this->createFormBuilder()
                        ->add('amount', 'choice', array(
                            'label' => 'Montant en euros',
                            'choices' => array(
                                1 => 1,
                                2 => 2,
                                10 => 10, 20 => 20, 50 => 50, 100 => 100, 200 => 200),
                            'preferred_choices' => array(10),
                        ))
                        //->add('currency', null, array('data' => 'EUR', 'label' => 'Devise'))
                        ->add('item_name', 'hidden', array(
                            'data' => 'Participation Au Blog MROOT',
                        ))
                        ->getForm()
        ;
    }

}
