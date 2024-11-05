<?php

namespace App\Repository;

use App\Entity\Video;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository; 
use Doctrine\Persistence\ManagerRegistry;
use App\Model\SearchData;    

/**
 * @extends ServiceEntityRepository<Video>
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    //    /**
    //     * @return Video[] Returns an array of Video objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Video
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

 //methode permettant de trouver une recette dans la barre de recherche 
 public function findBySearch(SearchData $searchData, ?User $user = null)
 {
     $data = $this->createQueryBuilder('r')
         ->addOrderBy('r.createdAt', 'DESC');
          
     if (!empty($searchData->query)) {
         $data = $data
             //recherche que sur le titre
            //  ->andWhere('r.title LIKE :query')
             //Si on veut rajouter aussi la recherche dans le contenu  - this does not work! Look below
            //  ->orWhere('r.content LIKE :query')

             // Search in both the title and description
             ->andWhere('r.title LIKE :query OR r.description LIKE :query')
             ->setParameter('query', "%{$searchData->query}%");
     }

     // If the user is not logged in (null), restrict to non-premium videos
     if (!$user) {
            $data = $data
                ->andWhere('r.premium = :nonPremium')
                ->setParameter('nonPremium', false);
    }


     return $data
         ->getQuery();
         // ->getResult(); 
 }

 // Method to count all videos (both premium and non-premium) depending on the user
//  public function countAllVideos(?User $user = null): int
//  {
//      $qb = $this->createQueryBuilder('v')
//          ->select('COUNT(v.id)');
     
//      // If the user is not logged in, only count non-premium videos
//      if (!$user) {
//          $qb->andWhere('v.premium = :nonPremium')
//             ->setParameter('nonPremium', false);
//      }
     
//      return (int) $qb->getQuery()->getSingleScalarResult();
//  }


}
