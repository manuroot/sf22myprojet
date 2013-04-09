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
}