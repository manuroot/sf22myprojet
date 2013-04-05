<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Application\Sonata\NewsBundle\Entity;
use Sonata\NewsBundle\Entity\PostManager as BasePostManager;
//use Sonata\NewsBundle\Model\PostManager as ModelPostManager;
//use Sonata\NewsBundle\Model\PostInterface;
/*use Sonata\NewsBundle\Model\BlogInterface;
use Sonata\NewsBundle\Model\CategoryInterface;
use Sonata\DoctrineORMAdminBundle\Datagrid\Pager;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;*/
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr;

use Doctrine\ORM\Query;

//class PostManager extends ModelPostManager 
class PostManager extends BasePostManager 
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param string                      $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;
    }

  
/*
      public function getPagerquery(array $criteria, $page, $maxPerPage = 10)
    {
        $parameters = array();
        $query=$this->em->getRepository($this->class)
            ->createQueryBuilder('p')
            ->select('p, t')
            ->leftJoin('p.tags', 't', Expr\Join::WITH, 't.enabled = true')
            ->leftJoin('p.author', 'a', Expr\Join::WITH, 'a.enabled = true')
          ->orderby('p.publicationDateStart', 'DESC');

        // enabled
        $criteria['enabled'] = isset($criteria['enabled']) ? $criteria['enabled'] : true;
        $query->andWhere('p.enabled = :enabled');
        $parameters['enabled'] = $criteria['enabled'];

        if (isset($criteria['date'])) {
            $query->andWhere($criteria['date']['query']);
            $parameters = array_merge($parameters, $criteria['date']['params']);
        }

        if (isset($criteria['tag'])) {
            $query->andWhere('t.slug LIKE :tag');
            $parameters['tag'] = (string) $criteria['tag'];
        }

        if (isset($criteria['author'])) {
            if (!is_array($criteria['author']) && stristr($criteria['author'], 'NULL')) {
                $query->andWhere('p.author IS '.$criteria['author']);
            } else {
                $query->andWhere(sprintf('p.author IN (%s)', implode((array) $criteria['author'], ',')));
            }
        }

        if (isset($criteria['category']) && $criteria['category'] instanceof CategoryInterface) {
            $query->andWhere('p.category = :categoryid');
         //   $parameters['categoryid'] = $criteria['category']->getId();
        }

       $query->setParameters($parameters);
        $query->setMaxResults( $maxPerPage);
      return $query;
        
//return $pager;
    }*/

   
}
