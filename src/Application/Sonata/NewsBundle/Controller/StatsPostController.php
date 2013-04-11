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
use \Sonata\NewsBundle\Form\Type\CommentType;

class StatsPostController extends Controller {

    /* ====================================================================
     * 
     *  SIDEBAR : TAGS, COMMENTS, CATEGORIES
     * 
      =================================================================== */

  

    /**
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
                ), $parameters);
        $response = $this->render(sprintf('SonataNewsBundle:Post:archive.%s.twig', $this->getRequest()->getRequestFormat()), $parameters);

        if ('rss' === $this->getRequest()->getRequestFormat()) {
            $response->headers->set('Content-Type', 'application/rss+xml');
        }

        return $response;
    }
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $week_ago = new DateTime('-1 week');
        $month_ago = new DateTime('-1 month');
        $year_ago = new DateTime('-1 year');
        $first_time = new DateTime('1th January 1970 00:00:00 (UTC)');

        return array(
            'post' => array(
                'last_week' => $em->getRepository('BlogBundle:Post')
                        ->countFromDate($week_ago),
                'last_month' => $em->getRepository('BlogBundle:Post')
                        ->countFromDate($month_ago),
                'last_year' => $em->getRepository('BlogBundle:Post')
                        ->countFromDate($year_ago),
                'total' => $em->getRepository('BlogBundle:Post')
                        ->countFromDate($first_time),
            ),
            'comment' => array(
                'last_week' => $em->getRepository('BlogBundle:Comment')
                        ->countFromDate($week_ago),
                'last_month' => $em->getRepository('BlogBundle:Comment')
                        ->countFromDate($month_ago),
                'last_year' => $em->getRepository('BlogBundle:Comment')
                        ->countFromDate($year_ago),
                'total' => $em->getRepository('BlogBundle:Comment')
                        ->countFromDate($first_time),
            ),
            'link' => array(
                'last_week' => $em->getRepository('BlogBundle:Link')
                        ->countFromDate($week_ago),
                'last_month' => $em->getRepository('BlogBundle:Link')
                        ->countFromDate($month_ago),
                'last_year' => $em->getRepository('BlogBundle:Link')
                        ->countFromDate($year_ago),
                'total' => $em->getRepository('BlogBundle:Link')
                        ->countFromDate($first_time),
            ),
        );
    }

  
}
