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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\NewsBundle\Model\PostInterface;

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
        $form_paypal = $this->createPurchaseForm();
        $pager = $this->getPostManager()->getPager(
                $criteria, $this->getRequest()->get('page', 1)
        );
        /*
          $paginator = $this->get('knp_paginator');
      $pagination = $paginator->paginate(
                $query, $this->get('request')->query->get('page', 1)
        );
        $pagination->setTemplate('ApplicationCertificatsBundle:pagination:sliding.html.twig');
          */
          
        $test = "(surcharge du controleur: phase de développement)";
        /*   $em = $this->container->get('doctrine')->getEntityManager();
          $allcategories = $em->getRepository('ApplicationSonataNewsBundle:Category')->findAll(); */
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

    /**
     * @return Response
     */
    public function archiveAction() {
        return $this->renderArchive();
    }

    /**
     * @param string $tag
     *
     * @return Response
     */
    public function tagAction($tag) {
        $tag = $this->get('sonata.news.manager.tag')->findOneBy(array(
            'slug' => $tag,
            'enabled' => true
                ));

        if (!$tag) {
            throw new NotFoundHttpException('Unable to find the tag');
        }

        if (!$tag->getEnabled()) {
            throw new NotFoundHttpException('Unable to find the tag');
        }

        return $this->renderArchive(array('tag' => $tag), array('tag' => $tag));
    }

    /**
     * @param string $category
     *
     * @return Response
     */
    public function categoryAction($category) {


        $em = $this->getDoctrine()->getManager();


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
     * @return \Sonata\SeoBundle\Seo\SeoPageInterface
     */
    public function getSeoPage() {
        if ($this->has('sonata.seo.page')) {
            return $this->get('sonata.seo.page');
        }

        return null;
    }

    /**
     * @param integer $postId
     *
     * @return Response
     */
    public function commentsAction($postId) {
        $pager = $this->getCommentManager()
                ->getPager(array(
            'postId' => $postId,
            'status' => CommentInterface::STATUS_VALID
                ), 1, 500); //no limit

        return $this->render('SonataNewsBundle:Post:comments.html.twig', array(
                    'pager' => $pager,
                ));
    }

    /**
     * @param $postId
     * @param bool $form
     *
     * @return Response
     */
    public function addCommentFormAction($postId, $form = false) {
        if (!$form) {
            $post = $this->getPostManager()->findOneBy(array(
                'id' => $postId
                    ));

            $form = $this->getCommentForm($post);
        }

        return $this->render('SonataNewsBundle:Post:comment_form.html.twig', array(
                    'form' => $form->createView(),
                    'post_id' => $postId
                ));
    }

    /**
     * @param $post
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getCommentForm(PostInterface $post) {
        $comment = $this->getCommentManager()->create();
        $comment->setPost($post);
        $comment->setStatus($post->getCommentsDefaultStatus());

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

            $this->getCommentManager()->save($comment);
            $this->get('sonata.news.mailer')->sendCommentNotification($comment);

            // todo : add notice
            return new RedirectResponse($this->generateUrl('sonata_news_view', array(
                                'permalink' => $this->getBlog()->getPermalinkGenerator()->generate($post)
                            )));
        }

        return $this->render('SonataNewsBundle:Post:view.html.twig', array(
                    'post' => $post,
                    'form' => $form
                ));
    }

    /**
     * @return \Sonata\NewsBundle\Model\PostManagerInterface
     */
    protected function getPostManager() {
        return $this->get('sonata.news.manager.post');
    }

    /**
     * @return \Sonata\NewsBundle\Model\CommentManagerInterface
     */
    protected function getCommentManager() {
        return $this->get('sonata.news.manager.comment');
    }

    /**
     * @return \Sonata\NewsBundle\Model\BlogInterface
     */
    protected function getBlog() {
        return $this->container->get('sonata.news.blog');
    }

    /**
     * @param string $commentId
     * @param string $hash
     * @param string $status
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function commentModerationAction($commentId, $hash, $status) {
        $comment = $this->getCommentManager()->findOneBy(array('id' => $commentId));

        if (!$comment) {
            throw new AccessDeniedException();
        }

        $computedHash = $this->get('sonata.news.hash.generator')->generate($comment);

        if ($computedHash != $hash) {
            throw new AccessDeniedException();
        }

        $comment->setStatus($status);

        $this->getCommentManager()->save($comment);

        return new RedirectResponse($this->generateUrl('sonata_news_view', array(
                            'permalink' => $this->getBlog()->getPermalinkGenerator()->generate($comment->getPost())
                        )));
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
