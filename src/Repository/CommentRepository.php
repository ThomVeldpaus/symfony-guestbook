<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Conference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * The CommentRepository is the class where you can filter out
 * certain comments by filter parameters, or even get all of
 * the comments at once.
 *
 * It's also possible to fetch just one result, the first match
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    // This constant will decide the max amount of comments per page
    public const PAGINATOR_PER_PAGE = 2;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * To have pages with the comments, I give Doctrine a Paginator
     * instead of a collection.
     *
     * @param Conference $conference
     * @param int $offset
     * @return Paginator
     */
    public function getCommentPaginator(Conference $conference, int $offset): Paginator
    {
        // This query selects all comments by parameters and filters
        $query = $this->createQueryBuilder('c')
        ->andWhere('c.conference = :conference')
        ->setParameter('conference', $conference)
        ->orderBy('c.createdAt', 'DESC')
        ->setMaxResults(self::PAGINATOR_PER_PAGE)
        ->setFirstResult($offset)
        ->getQuery();

        //The Paginator returns the result of the queryBuilder in a Paginated manner
        return new Paginator($query);
    }


    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
