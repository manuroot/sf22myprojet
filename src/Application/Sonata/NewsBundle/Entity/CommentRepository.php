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
use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{

    public function FindLastComments($limit=10) {
        return $this->createQueryBuilder('p')
                
                //  ->add('orderBy', 'p.id DESC')
                 ->where('p.status = 1')
                 ->orderby('p.updatedAt', 'DESC')
                  ->setMaxResults($limit)
                        ->getQuery()
                 ->getResult();
/*
 * $parameters = array();

        $query = $this->em->getRepository($this->class)
            ->createQueryBuilder('c')
            ->orderby('c.createdAt', 'DESC');

        $criteria['status'] = isset($criteria['status']) ? $criteria['status'] : CommentInterface::STATUS_VALID;
        $query->andWhere('c.status = :status');
        $parameters['status'] = $criteria['status'];

        if (isset($criteria['postId'])) {
            $query->andWhere('c.post = :postId');
            $parameters['postId'] = $criteria['postId'];
        }
 */
        //->getResult();
    }
}