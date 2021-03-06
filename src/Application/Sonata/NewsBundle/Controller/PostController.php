<?php

// Doctrine2 DQL
//     "doctrine/doctrine-fixtures-bundle": "dev-master",

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
use Symfony\Component\HttpFoundation\Request;
use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\NewsBundle\Model\PostInterface;
use Doctrine\ORM\EntityRepository;
use Sonata\NewsBundle\Controller\PostController as Controller;
use Application\Sonata\NewsBundle\Form\Type\CommentType;
use Application\Sonata\NewsBundle\Form\Type\PostFilterType;
use Application\Sonata\NewsBundle\Form\Type\EpostType;
use Application\Sonata\NewsBundle\Entity\Post;

class PostController extends Controller {

    /**
     * @return RedirectResponse
     */
    public function homeAction() {
        return $this->redirect($this->generateUrl('sonata_news_archive'));
    }

    private function getuserid() {


        $em = $this->getDoctrine()->getManager();
        $user_security = $this->container->get('security.context');
        // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
        if ($user_security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->get('security.context')->getToken()->getUser();
            $user_id = $user->getId();
            $group_id = $user->getIdgroup()->getId();
        } else {
            $user_id = 0;
            $group_id = 0;
        }
        return array($user_id, $group_id);
    }

    /* ====================================================================
     * 
     *  SIDEBAR : TAGS, COMMENTS, CATEGORIES
     * 
      =================================================================== */

    private function sidebar_tags() {

        $em = $this->container->get('doctrine')->getManager();
        $alltags = $em->getRepository('ApplicationSonataNewsBundle:Tag')->findByEnabled(1);
        $tagWeights = $em->getRepository('ApplicationSonataNewsBundle:Tag')->getTagWeights($alltags);
        return array($alltags, $tagWeights);
    }

    private function sidebar_comments() {
        $em = $this->container->get('doctrine')->getManager();
        list($user_id, $group_id) = $this->getuserid();
        //myFindAll
        //  $allcategories = $em->getRepository('ApplicationSonataNewsBundle:Category')->myFindAll();
        //$lastcomments = $em->getRepository('ApplicationSonataNewsBundle:Comment')->FindLastComments();
        if ($user_id != 0 && $group_id != 0) {
            $lastcomments = $em->getRepository('ApplicationSonataNewsBundle:Comment')->FindGroupLastComments(10, $group_id);
        } else {
            $lastcomments = $em->getRepository('ApplicationSonataNewsBundle:Comment')->FindLastComments();
        }
//FindGroupLastComments($limit=20,$group=null) 
        //    ->findAll();
        return ($lastcomments);
    }

    private function sidebar_categories() {
        $em = $this->container->get('doctrine')->getManager();
        //myFindAll
        //  $allcategories = $em->getRepository('ApplicationSonataNewsBundle:Category')->myFindAll();
        $allcategories = $em->getRepository('ApplicationSonataNewsBundle:Category')->findByEnabled(1);
        // $catWeights = $em->getRepository('ApplicationSonataNewsBundle:Category')->getCategoriesWeights($allcategories);
        //  return array($allcategories,$catWeights);
        return ($allcategories);
    }

    private function sidebar_years($max = 5) {
        $em = $this->container->get('doctrine')->getManager();


        $myarr = array();
        $myarr['current_year'] = date('Y');
        //  $myarr['first_year'] = date('Y'-1);
        // echo "year=" . $myarr['current_year'];
        //  echo "year=" . $myarr['first_year'];
        $arr_years = $em->getRepository('ApplicationSonataNewsBundle:Post')->findaByYear($myarr['current_year']);
        //$fist_year = $em->getRepository('ApplicationSonataNewsBundle:Post')->findaByYear($myarr['first_year']);
        // $count = count($post_year);
        /* $minyear=$myarr['year']-10;
          $myarr['month']=date('m');
          $myarr['dat']=date('dd');
         *
          for */
        //   echo "nb_year";
        //   print_r($arr_years);
        //     exit(1);
        return ($arr_years);
        //  return array($post_year, $myarr['year']);
    }

    /**
     * @param array $criteria
     *
     * @return Response
     */
    /* ===================================================================
     * 
     *  MAIN: RENDER + KNP_PAGINATOR
     * 
      =================================================================== */

    public function renderknpArchive(array $criteria = array(), array $parameters = array()) {


        $em = $this->container->get('doctrine')->getManager();
        //$form_paypal = $this->createPurchaseForm();
        $query = $em->getRepository('ApplicationSonataNewsBundle:Post')->getPager($criteria);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $this->get('request')->query->get('page', 1)/* page number */, 2/* limit per page */
        );

        $pagination->setTemplate('ApplicationSonataNewsBundle:pagination:twitter_bootstrap_pagination.html.twig');
        list($alltags, $tagWeights) = $this->sidebar_tags();
        $allcategories = $this->sidebar_categories();
        $lastcomments = $this->sidebar_comments();
        $parameters = array_merge(array(
            'blog' => $this->get('sonata.news.blog'),
            'tag' => false,
            //  'form_paypal' => $form_paypal->createView(),
            'allcategories' => $allcategories,
            // 'catweight' => $catWeights,
            'alltags' => $alltags,
            'lastcomments' => $lastcomments,
            'tagweight' => $tagWeights,
            'pager' => $pagination,
                ), $parameters);
        $response = $this->render(sprintf('SonataNewsBundle:Post:archiveknp.%s.twig', $this->getRequest()->getRequestFormat()), $parameters);

        if ('rss' === $this->getRequest()->getRequestFormat()) {
            $response->headers->set('Content-Type', 'application/rss+xml');
        }

        return $response;
    }

    /* ===================================================================
     * 
     *  MAIN: RENDER + PAGER
     * 
      =================================================================== */

    public function renderArchive(array $criteria = array(), array $parameters = array()) {

        $form_paypal = $this->createPurchaseForm();
        $pager = $this->getPostManager()->getPager(
                $criteria, $this->getRequest()->get('page', 1), 5
        );
        $test = "(surcharge du controleur: phase de développement)";
        list($alltags, $tagWeights) = $this->sidebar_tags();
        //list($allcategories,$catWeights) = $this->sidebar_categories();
        $allcategories = $this->sidebar_categories();
        $lastcomments = $this->sidebar_comments();
        //      list($post_year, $current_year) = $this->sidebar_years();
        $all_years = $this->sidebar_years();
        //  $first_year=$current_year-5;
        // $alltags=$em->getRepository('ApplicationSonataNewsBundle:Tag')->findAll();
        // $pager->setLinks(3);
        $parameters = array_merge(array(
            'pager' => $pager,
            'blog' => $this->get('sonata.news.blog'),
            'tag' => false,
            'test' => $test,
            'form_paypal' => $form_paypal->createView(),
            'allcategories' => $allcategories,
            // 'catweight' => $catWeights,
            'alltags' => $alltags,
            'lastcomments' => $lastcomments,
            'tagweight' => $tagWeights,
            'all_years' => $all_years,
            /* 'current_year'=>$current_year,
              'first_year'=>$first_year,
              'nb_year'=>5,
              'post_year'=>$post_year, */
            'route' => $this->getRequest()->get('_route'),
            'route_parameters' => $this->getRequest()->get('_route_params')
                ), $parameters);
        $response = $this->render(sprintf('SonataNewsBundle:Post:archive.%s.twig', $this->getRequest()->getRequestFormat()), $parameters);

        //$this->archivebymonth();
        if ('rss' === $this->getRequest()->getRequestFormat()) {
            $response->headers->set('Content-Type', 'application/rss+xml');
        }

        return $response;
    }

    /* ===================================================================
     * 
     *  MAIN: RENDER + DATAGRID
     * 
      =================================================================== */

//myFindOtherAll($user_id,$group_id) 
    public function mesnewsAction(array $criteria = array(), array $parameters = array()) {
        list($user_id, $group_id) = $this->getuserid();
        $form_paypal = $this->createPurchaseForm();

        $entity = new post();
        /*
          $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
          $path = $helper->asset($entity, 'image'); */
        $forma = $this->createForm(new EpostType(), $entity);


        $session = $this->getRequest()->getSession();
        $session->set('buttonretour', 'list_mesnews');


        $form = $this->get('form.factory')->create(new PostFilterType());
        //   $forma = $this->get('form.factory')->create(new EpostType());

        if ($this->get('request')->query->has('submit-filter')) {
            // bind values from the request
            $form->bindRequest($this->get('request'));
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('ApplicationSonataNewsBundle:Post')
                    ->createQueryBuilder('e');

            $query = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
        } else {
            $em = $this->getDoctrine()->getManager();
            // $query = $em->getRepository('ApplicationEpostBundle:Epost')->myFindAll($user_id);
            $query = $em->getRepository('ApplicationSonataNewsBundle:Post')->myFindaAll($user_id);
        }
        //   $alltags = $this->sidebar_tags();
        list($alltags, $tagWeights) = $this->sidebar_tags();
        $all_years = $this->sidebar_years();
        $allcategories = $this->sidebar_categories();
        $lastcomments = $this->sidebar_comments();
        // $paginator = $this->container->get("savvy.filter_nator");
        //  $pagination=$paginator->filterNate($filterBuilder, $form, 'foo',5,1);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $this->get('request')->query->get('page', 1), 10
        );

        $pagination->setTemplate('ApplicationSonataNewsBundle:pagination:twitter_bootstrap_pagination.html.twig');

        return $this->render('ApplicationSonataNewsBundle:Post:mesnews.html.twig', array(
                    // 'pager' => $mypager,
                    'pagination' => $pagination,
                    'form_paypal' => $form_paypal->createView(),
                    'allcategories' => $allcategories,
                    'lastcomments' => $lastcomments,
                    'alltags' => $alltags,
                    'tagweight' => $tagWeights,
                    'all_years' => $all_years,
                    'form' => $form->createView(),
                    'forma' => $forma->createView(),
                ));
    }

    public function mongroupnewsAction(array $criteria = array(), array $parameters = array()) {

        list($user_id, $group_id) = $this->getuserid();
        $session = $this->getRequest()->getSession();
        $session->set('buttonretour', 'list_mongroupnews');

        $form_paypal = $this->createPurchaseForm();
        $form = $this->get('form.factory')->create(new PostFilterType());
        if ($this->get('request')->query->has('submit-filter')) {
            // bind values from the request
            $form->bindRequest($this->get('request'));
            $filterBuilder = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('ApplicationSonataNewsBundle:Post')
                    ->createQueryBuilder('e');
            $query = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $filterBuilder);
        } else {
            $em = $this->getDoctrine()->getManager();
            // $query = $em->getRepository('ApplicationEpostBundle:Epost')->myFindAll($user_id);
            $query = $em->getRepository('ApplicationSonataNewsBundle:Post')->myFindOtherAll($user_id, $group_id);
        }
        //   $alltags = $this->sidebar_tags();
        list($alltags, $tagWeights) = $this->sidebar_tags();
        $all_years = $this->sidebar_years();
        $allcategories = $this->sidebar_categories();
        $lastcomments = $this->sidebar_comments();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $this->get('request')->query->get('page', 1), 10
        );

        $pagination->setTemplate('ApplicationSonataNewsBundle:pagination:twitter_bootstrap_pagination.html.twig');

        return $this->render('ApplicationSonataNewsBundle:Post:mongroupnews.html.twig', array(
                    // 'pager' => $mypager,
                    'pagination' => $pagination,
                    'form_paypal' => $form_paypal->createView(),
                    'allcategories' => $allcategories,
                    'lastcomments' => $lastcomments,
                    'alltags' => $alltags,
                    'tagweight' => $tagWeights,
                    'all_years' => $all_years,
                    'form' => $form->createView(),
                ));
    }

    /**
     * @throws NotFoundHttpException
     *
     * @param $permalink
     *
     * @return Response
     */
    /* ===================================================================
     * 
     *  VIEW
     * 
      =================================================================== */

    public function viewAction($permalink) {
        $form_paypal = $this->createPurchaseForm();
        $post = $this->getPostManager()->findOneByPermalink($permalink, $this->container->get('sonata.news.blog'));

        if (!$post || !$post->isPublic()) {
            throw new NotFoundHttpException('Unable to find the post');
        }
        //   $alltags = $this->sidebar_tags();
        list($alltags, $tagWeights) = $this->sidebar_tags();

        $allcategories = $this->sidebar_categories();
        $lastcomments = $this->sidebar_comments();
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
        /*

          ADD: tout sur la meme page
         */

        $page = $this->getRequest()->get('page', 1);
        $pager = $this->getCommentManager()
                ->getPager(array(
            'postId' => $post->getId(),
            'status' => CommentInterface::STATUS_VALID
                ), $page, 5); //no limit



        return $this->render('SonataNewsBundle:Post:view.html.twig', array(
                    'post' => $post,
                    'form' => false,
                    'blog' => $this->get('sonata.news.blog'),
                    'form_paypal' => $form_paypal->createView(),
                    'pager' => $pager,
                    'allcategories' => $allcategories,
                    'alltags' => $alltags,
                    'lastcomments' => $lastcomments,
                    'tagweight' => $tagWeights,
                ));
    }

    /**
     * @param $category
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function categoryAction($category) {
        $category = $this->get('sonata.news.manager.category')->findOneBy(array(
            'slug' => $category,
            'enabled' => true
                ));

        if (!$category) {
            throw new NotFoundHttpException('Unable to find the category');
        }

        if (!$category->getEnabled()) {
            throw new NotFoundHttpException('Unable to find the category');
        }

        return $this->renderArchive(array('category' => $category), array('category' => $category));
    }

    /**
     * @param integer $postId
     *
     * @return Response
     */
    /*   public function mycommentsAction($mpost, $postId) {
      $pager = $this->getCommentManager()
      ->getPager(array(
      'postId' => $postId,
      'status' => CommentInterface::STATUS_VALID
      ), 1, 5); //no limit

      return $this->render('SonataNewsBundle:Post:comments.html.twig', array(
      'pager' => $pager,
      'mpost' => $mpost,
      ));
      } */

    public function commentsAction($postId) {
        $pager = $this->getCommentManager()
                ->getPager(array(
            'postId' => $postId,
            'status' => CommentInterface::STATUS_VALID
                ), 1, 500); //no limit

        return $this->render('SonataNewsBundle:Post:comments.html.twig', array(
                    'pager' => $pager,
                    'postId' => $postId,
                ));
    }

    /**
     * @param $postId
     * @param bool $form
     *
     * @return Response
     */

    /**
     * @param $post
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getMyCommentForm(PostInterface $post, $user, $email) {
        $comment = $this->getCommentManager()->create();
        $comment->setPost($post);
        $comment->setStatus($post->getCommentsDefaultStatus());
        if (isset($user))
            $comment->setName($user);
        if (isset($email))
            $comment->setEmail($email);
        //  print_r($user);
        //   exit(1);
        return $this->get('form.factory')->createNamed('comment', 'sonata_post_comment', $comment);
    }

    /**
     * @throws NotFoundHttpException
     *
     * @param string $id
     *
     * @return Response
     */
    public function addCommentAction($id) {
        $post = $this->getPostManager()->findOneBy(array(
            'id' => $id
                ));

        if (!$post) {
            throw new NotFoundHttpException(sprintf('Post (%d) not found', $id));
        }

        if (!$post->isCommentable()) {
            // todo add notice
            return new RedirectResponse($this->generateUrl('sonata_news_view', array(
                                'permalink' => $this->getBlog()->getPermalinkGenerator()->generate($post)
                            )));
        }

        $form = $this->getCommentForm($post);
        $form->bindRequest($this->get('request'));

        if ($form->isValid()) {
            $comment = $form->getData();
            // envoi de mail par ajout de commentaire
            $this->getCommentManager()->save($comment);
            $this->get('sonata.news.mailer')->sendCommentNotification($comment);

            // todo : add notice
            return new RedirectResponse($this->generateUrl('sonata_news_view', array(
                                'permalink' => $this->getBlog()->getPermalinkGenerator()->generate($post)
                            )));
        } else {
            return new RedirectResponse($this->generateUrl('sonata_news_view', array(
                                'permalink' => $this->getBlog()->getPermalinkGenerator()->generate($post)
                            )));
        }
    }

    public function addCommentFormAction($postId, $form = false) {
        //    parent::addCommentFormAction($postId, $form=false);
        if (!$form) {
            $post = $this->getPostManager()->findOneBy(array(
                'id' => $postId
                    ));
            //$options=array();
            $user_name = "unknown";
            $user = $this->get('security.context')->getToken()->getUser();
            $user_security = $this->container->get('security.context');
            //if( $user_security->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
            if ($user_security->isGranted('IS_AUTHENTICATED_FULLY')) {
                //     $user_id = $user->getId();
                $user_name = $user->getUsername();
                $user_email = $user->getEmail();
            }
            $form = $this->getMyCommentForm($post, $user_name, $user_email);
        }

        return $this->render('SonataNewsBundle:Post:comment_form.html.twig', array(
                    'form' => $form->createView(),
                    'post_id' => $postId
                ));
    }

    /* ===================================================================
     * 
     *  PAYPAL FORM
     * 
      =================================================================== */

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

    public function archivebymonth() {

        $myarr = array();
        $myarr['year'] = date('Y');
        $myarr['month'] = date('m');
        $myarr['dat'] = date('dd');


        print_r($myarr);
        exit(1);
        /* $now=new DateTime();
          $week_ago = new DateTime('-1 week');
          $month_ago = new DateTime('-1 month');
          $year_ago = new DateTime('-1 year');
          $first_time = new DateTime('1th January 1970 00:00:00 (UTC)');
         */
    }

    /**
     * @param string $year
     * @param string $month
     *
     * @return Response
     */
    public function archiveMonthlyAction($year, $month) {
        return $this->renderArchive(array(
                    'date' => $this->getPostManager()->getPublicationDateQueryParts(sprintf('%d-%d-%d', $year, $month, 1), 'month')
                ));
    }

    /**
     * @param string $year
     *
     * @return Response
     */
    public function archiveYearlyAction($year) {
        return $this->renderArchive(array(
                    'date' => $this->getPostManager()->getPublicationDateQueryParts(sprintf('%d-%d-%d', $year, 1, 1), 'year')
                ));
    }

    public function eposteditAction($id) {
        $em = $this->getDoctrine()->getManager();
        $form_paypal = $this->createPurchaseForm();
        //$query = $em->getRepository('ApplicationSonataNewsBundle:Post')->myFindaAll($user_id);
        $entity = $em->getRepository('ApplicationSonataNewsBundle:Post')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Epost entity.');
        }
        $session = $this->getRequest()->getSession();
        $myretour = $session->get('buttonretour');

        list($user_id, $group_id) = $this->getuserid();
        $proprietaire = $entity->getAuthor()->getId();
        //echo "u=$user_id  p=$proprietaire<br>";
        //    exit(1);
        if ($user_id != $proprietaire) {
            return $this->render('ApplicationSonataNewsBundle:Post:deny.html.twig', array(
                    ));
        }

        //    $entity = new post();
        /*
          $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
          $path = $helper->asset($entity, 'image'); */
        $editForm = $this->createForm(new EpostType(), $entity);

        //  $editForm = $this->createForm(new EpostType(), $entity);
        // $deleteForm = $this->createDeleteForm($id);

        return $this->render('ApplicationSonataNewsBundle:Post:epostedit.html.twig', array(
                    'entity' => $entity,
                    'btnretour' => $myretour,
                    'edit_form' => $editForm->createView(),
                    'form_paypal' => $form_paypal->createView(),
                        // 'delete_form' => $deleteForm->createView(),
                ));
    }

    /**
     * Edits an existing Epost entity.
     *
     */
    public function epostupdateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ApplicationSonataNewsBundle:Post')->find($id);

//        $entity = $em->getRepository('ApplicationEpostBundle:Epost')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Epost entity.');
        }

        // $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EpostType(), $entity);
        $editForm->bind($request);
        $session = $this->getRequest()->getSession();
        $myretour = $session->get('buttonretour');
        if (!isset($myretour))
            $myretour = 'list_mesnews';
        if ($editForm->isValid()) {
            
            $postData = $request->request->get('application_sonata_eposttype');
      //   print_r($postData);
     //   exit(1);
         
            //$entity->setUpdatedAt(new \DateTime());
            $em->persist($entity);
            $em->flush();

            $session->getFlashBag()->add('warning', "Enregistrement $id update successfull");
            $route_back = $session->get('buttonretour');
            if (isset($route_back))
                return $this->redirect($this->generateUrl($route_back, array('id' => $id)));
            else
                return $this->redirect($this->generateUrl('list_mesnews', array('id' => $id)));
        }

        return $this->render('ApplicationSonataNewsBundle:Post:epostedit.html.twig', array(
                    'entity' => $entity,
                    'btnretour' => $myretour,
                    'edit_form' => $editForm->createView(),
                        // 'delete_form' => $deleteForm->createView(),
                ));
    }

    /*Hi I have an application where users can add videos and pictures. I am
using SonataMediaBundle but since those users don't have access to the
admin dashboard I have to create the form they are using to upload photos
and videos. */
    
    public function addvideoAction(Request $request, $id)
    {
        $defaultData = array('uri' => 'paste the video url', );
        $form = $this->createFormBuilder($defaultData)
            ->add('uri','url')
            ->getForm();
       if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $data = $form->getData();
                $uri = $data['uri'];
                $videoprovider = $this->FindVideoProvider($uri);
//FindVideoProvider is just a method that determines if the video comes
//from youtube vimeo or dailymotion
                if (! $videoprovider === false ){
                    $em = $this->getDoctrine()->getManager();
                    $mediaManager = $this->get('sonata.media.manager.media');
                    $media = $mediaManager->create();
                    $media->setBinaryContent($uri);
                    $media->setContext('default');
                    $media->setProviderName($videoprovider);
                    $mediaManager->save($media);
                    $foo->addVideo($media);
                    $em->flush();
                }
            }
        }

        return array(
            'form' => $form->createView(),
            'run' => $run
            );
    }


   
public function addmyimageAction(Request $request,$id)
    {
        $form = $this->createFormBuilder()
        ->add('binarycontent','file')
        ->getForm();

          $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ApplicationSonataNewsBundle:Post')->find($id);
 if (!$entity) {
            throw $this->createNotFoundException('Unable to find Epost entity.');
        }
  
      if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $data = $form->getData();
                $binarycontent = $data['binarycontent'];

                $em = $this->getDoctrine()->getManager();
                $mediaManager = $this->get('sonata.media.manager.media');

                $photo = $mediaManager->create();
                $photo->setBinaryContent($binarycontent);
                $photo->setContext('default');
                $photo->setProviderName('sonata.media.provider.image');
                $mediaManager->save($photo);
                
                $entity->setImage($photo);
                
                //   public function setImage(\Application\Sonata\MediaBundle\Entity\Media $image = null)
  
                $em->flush();
                 return $this->render('ApplicationSonataNewsBundle:Post:addimage.html.twig', array(
                    'form' => $form->createView(),
              'entity' => $entity,
                        // 'delete_form' => $deleteForm->createView(),
                ));
              //  list_mesnews
            }
        }

         return $this->render('ApplicationSonataNewsBundle:Post:addimage.html.twig', array(
                    'form' => $form->createView(),
              'entity' => $entity,
                        // 'delete_form' => $deleteForm->createView(),
                ));
    
       /* return array(
            'form' => $form->createView(),
            'run' => $run
            );*/
    } 
}
