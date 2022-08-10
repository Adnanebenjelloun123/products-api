<?php

namespace App\Repository;

use App\Entity\Newconversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use DateTime;

/**
 * @extends ServiceEntityRepository<Newconversation>
 *
 * @method Newconversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Newconversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Newconversation[]    findAll()
 * @method Newconversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewconversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        parent::__construct($registry, Newconversation::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Newconversation $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Newconversation $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    public function conversationsStats(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $user = $this->tokenStorage->getToken()->getUser()->getEmail();
        if (isset($_GET['from'])) {
            $fromdate = $_GET['from'];
        }
        if (!isset($_GET['to'])) {
            $todate = date("Y/m/d");
        } else {
            $todate = $_GET['to'];
        }
        if (isset($_GET['from']) || isset($_GET['to'])) {
            $sql = '
            SELECT COUNT(*) 
             FROM newconversation 
             WHERE parent_brand LIKE "' . $user . '"
             AND created_at >= "' . $fromdate . '" AND created_at <= "' . $todate . '"
             GROUP BY parent_brand
            UNION ALL
            SELECT COUNT(*) 
             FROM newconversation 
             WHERE parent_brand LIKE "' . $user . '"
             AND completed LIKE "YES" 
             AND source LIKE "whatsapp"
             AND created_at >= "' . $fromdate . '" AND created_at <= "' . $todate . '"
             GROUP BY parent_brand;
            UNION  ALL SELECT COUNT(*)
            FROM newconversation 
            WHERE parent_brand LIKE "' . $user . '"
            AND completed LIKE "NO"
            AND source LIKE "whatsapp"
            AND created_at >= "' . $fromdate . '" AND created_at <= "' . $todate . '"
            GROUP BY parent_brand;
            UNION  ALL SELECT COUNT(*) 
            FROM newconversation 
            WHERE parent_brand LIKE "' . $user . '"
            AND completed LIKE "YES"
            AND source LIKE "web"
            AND created_at >= "' . $fromdate . '" AND created_at <= "' . $todate . '"
            GROUP BY parent_brand;
            UNION ALL SELECT COUNT(*) 
            FROM newconversation 
            WHERE parent_brand LIKE "' . $user . '"
            AND completed LIKE "NO"
            AND source LIKE "web"
            AND created_at >= "' . $fromdate . '" AND created_at <= "' . $todate . '"
            GROUP BY parent_brand;
            ';
        } else {
            $sql = '
            SELECT COUNT(*) 
            FROM newconversation 
            WHERE parent_brand LIKE "' . $user . '"
            GROUP BY parent_brand
            UNION ALL
            SELECT COUNT(*) 
             FROM newconversation 
             WHERE parent_brand LIKE "' . $user . '"
             AND completed LIKE "YES" 
             AND source LIKE "whatsapp"
             GROUP BY parent_brand
            UNION  ALL 
            SELECT COUNT(*)
            FROM newconversation 
            WHERE parent_brand LIKE "' . $user . '"
            AND completed LIKE "NO"
            AND source LIKE "whatsapp"
            GROUP BY parent_brand
            UNION ALL 
            SELECT COUNT(*) 
            FROM newconversation 
            WHERE parent_brand LIKE "' . $user . '"
            AND completed LIKE "YES"
            AND source LIKE "web"
            GROUP BY parent_brand
            UNION ALL 
            SELECT COUNT(*) 
            FROM newconversation 
            WHERE parent_brand LIKE "' . $user . '"
            AND completed LIKE "NO"
            AND source LIKE "web"
            GROUP BY parent_brand
             ';
        }

      
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }
    public function countbyBrand(): array
    {
        $conn = $this->getEntityManager()->getConnection();
       
        if (isset($_GET['from'])) {
            $fromdate = $_GET['from'];
        }
        if (!isset($_GET['to'])) {
            $todate = date("Y/m/d");
        } else {
            if($_GET['to'] != $_GET['from']){
                $todate = $_GET['to'];
            }
            else{
                $original_date = $_GET['to'];
                $time_original = strtotime($original_date);
                $time_add      = $time_original + (3600*24); //add seconds of one day
                $new_date      = date("Y-m-d", $time_add);                         
                $todate = $new_date;
            }
            
        }
        $brand = $_GET['brand'];
        if($brand === "1"){
            $brand ="fiat";
        }
        else if($brand === "2"){
            $brand ="jeep";
        }
        else if($brand === "3"){
            $brand ="alfaromeo";
        }
        else if($brand === "5"){
            $brand ="fiatpro";
        }
        
        if (isset($_GET['from']) || isset($_GET['to'])) {
            if(isset($_GET['brand'])){
            $sql = '
            SELECT COUNT(*) 
             FROM newconversation 
             WHERE parent_brand = "' . $_GET['parent'] . '"
             AND brand LIKE "' . $brand . '"
             AND created_at >= "' . $fromdate . '" AND created_at <= "' . $todate . '"
             GROUP BY parent_brand;
            ';
            }
            else{
            $sql = '
            SELECT COUNT(*) 
             FROM newconversation 
             WHERE parent_brand = "' . $_GET['parent'] . '"
             AND created_at >= "' . $fromdate . '" AND created_at <= "' . $todate . '"
             GROUP BY parent_brand;
            ';
            }
        } else {
            if(isset($_GET['brand'])){
                $sql = '
                SELECT COUNT(*) 
                 FROM newconversation 
                 WHERE parent_brand = "' . $_GET['parent'] . '"
                 AND brand LIKE "' . $brand . '"
                 GROUP BY parent_brand;
                ';
            }
            else {
            $sql = '
            SELECT COUNT(*) 
             FROM newconversation 
             WHERE parent_brand = "' . $_GET['parent'] . '"
             GROUP BY parent_brand;
            ';
            }
        }


        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }
    // /**
    //  * @return Newconversation[] Returns an array of Newconversation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Newconversation
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
