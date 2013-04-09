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

class CategoryRepository extends EntityRepository
{

    public function myFindAll() {
        return $this->createQueryBuilder('p')
                
                //  ->add('orderBy', 'p.id DESC')
                 ->where('p.enabled = 1')
                
            ->orderby('p.name', 'DESC')
                        ->getQuery()
                 ->getResult();

        //->getResult();
    }

}