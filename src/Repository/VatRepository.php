<?php

namespace App\Repository;

use App\Entity\Vat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vat>
 *
 * @method Vat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vat[]    findAll()
 * @method Vat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vat::class);
    }
}
