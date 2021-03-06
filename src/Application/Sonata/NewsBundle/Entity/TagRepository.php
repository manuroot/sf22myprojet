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

class TagRepository extends BasePostRepository
{

    public function myFindAll() {
        return $this->createQueryBuilder('p')
                
                //  ->add('orderBy', 'p.id DESC')
                 ->where('p.enabled = 1')
               ->orderby('p.createdAt', 'DESC')
                        ->getQuery();

        //->getResult();
    }
public function getTags()
{
    $blogTags = $this->createQueryBuilder('b')
                     ->select('b.tags')
                     ->getQuery()
                     ->getResult();

    $tags = array();
    foreach ($blogTags as $blogTag)
    {
   //     $tags = array_merge(explode(",", $blogTag['tags']), $tags);
            $tags = $blogTag['tags'];
    }

    foreach ($tags as &$tag)
    {
        $tag = trim($tag);
    }

    return $tags;
}

public function getTagWeights($tags)
{
        //getPosts()
        $tagWeights=array();
        $aa=array();
        foreach ($tags as $tag)
    {
         $a=$tag->getPosts();
         $b=count($a);
         $tagWeights[$tag->getName()] =$b;
      }
 // Shuffle the tags
   /* uksort($tagWeights, function() {
        return rand() > rand();
    }); */   
     $max = max($tagWeights);
    // Max of 5 weights
    $multiplier = ($max > 5) ? 5 / $max : 1;
    foreach ($tagWeights as &$tag)
    {
        $tag = ceil($tag * $multiplier);
    }

  return $tagWeights;
}
}