<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\NewsBundle\Entity;
use Sonata\NewsBundle\Entity\BasePostRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr;

use Doctrine\ORM\Query;

class PostRepository extends BasePostRepository
{

    public function myFindAll() {
        return $this->createQueryBuilder('p')
                
                //  ->add('orderBy', 'p.id DESC')
                 ->where('p.enabled = true')
                ->leftJoin('p.category', 'd')
                ->leftJoin('p.author', 'e')
                
            ->orderby('p.createdAt', 'DESC')
                        ->getQuery();

        //->getResult();
    }
    
    
    /**
* Count published elements from date
*
* @param DateTime $date
* @return int
*/
    public function countFromDate(DateTime $date) {
       
        $query = $this->createQueryBuilder('p')
            ->select('p, t')
            ->leftJoin('p.tags', 't', Expr\Join::WITH, 't.enabled = true')
            ->orderby('p.publicationDateStart', 'DESC');

        $query = $em->createQuery(
                        ' SELECT COUNT(p) FROM BlogBundle:Post p ' .
                        ' WHERE p.isPublished = 1 ' .
                        ' AND p.createdAt >= :date '
                )
                ->setParameter('date', $date)
        ;
        return $query->getSingleScalarResult();
    }


    
       public function getPager(array $criteria, $maxPerPage = 10)
    {
        $parameters = array();
        $query = $this->createQueryBuilder('p')
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
            $parameters['categoryid'] = $criteria['category']->getId();
        }

        $query->setParameters($parameters);
       return $query;
    }

}