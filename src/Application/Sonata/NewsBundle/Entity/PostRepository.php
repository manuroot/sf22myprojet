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
//use Doctrine\ORM\EntityRepository;
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

// .........
// Doctrine2 repository class for entity 'Post'
// .........
   // public function findOneByYearMonthDay($year, $month, $day)
    /*
public function findcaByYear($year)
{
   $year="2013";
    $query = $this->createQueryBuilder('p')
        ->where('p.publicationDateStart LIKE %2013%');
    $q=$query->getQuery()->getResult();
   // print($q);
    foreach ($q as $i){print_r($i);}
    exit(1);
  
    return $query->getResult();
}*/


public function findaByYear($year){
   /*  $emConfig = $this->getEntityManager()->getConfiguration();
    $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
    $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
    $emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');
    */
  //  $year=2013;
      $qb = $this->createQueryBuilder('p');
    //$qb->select('COUNT(p)')
            $qb->select('p.id,p.publicationDateStart');
    //   ->where('YEAR(p.publicationDateStart) = :year');
  //  $qb->setParameter('year', $year);
   $arr=array();
  //  ->getSingleScalarResult();
//print_r($qb->getQuery()->getResult());
   // $arr["$year"]=0;
  
foreach ($qb->getQuery()->getResult() as $d){
    
   // echo $d['publicationDateStart']->format('Y-m-d H:i:s') . "<br>";
  //   echo $d['publicationDateStart']->format('Y') . "<br>";
      $year=$d['publicationDateStart']->format('Y');
        if (!(isset($arr["$year"]))) $arr["$year"]=0;
        $arr["$year"]=$arr["$year"]+1;
    //  $arr[$year]= isset($arr[$year]) ? ($arr[$year]++) : '1';
    /* if (isset($year)){
    //     $temp=$d['publicationDateStart']->format('Y');
         $arr["$year"]+=1;
      //   $arr[$year]= isset($arr[$year]) ? ($arr[$year]++) : 1;
     }*/
}
//print_r($arr);
//exit(1);
return ($arr);
        return $query->getResult();

        
        /*return $this->createQueryBuilder('a')
 ->select('COUNT(a)')
 ->getQuery()
 ->getSingleScalarResult();
*/
 }
}