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

public function getCategoriesWeights($categories)
{
        //getPosts()
        $catWeights=array();
        $aa=array();
        foreach ($categories as $cat)
    {
         $a=$cat->getPosts();
         $b=count($a);
         $catWeights[$cat->getSlug()] =$b;
      }
     $max = max($catWeights);
    // Max of 5 weights
    $multiplier = ($max > 5) ? 5 / $max : 1;
    foreach ($catWeights as &$cat)
    {
        $cat = ceil($cat * $multiplier);
    }
    /*
print_r($catWeights);
exit(1);*/
  return $catWeights;
}
}