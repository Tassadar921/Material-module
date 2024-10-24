<?php

namespace App\Repository;

use App\Entity\Material;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Material>
 *
 * @method Material|null find($id, $lockMode = null, $lockVersion = null)
 * @method Material|null findOneBy(array $criteria, array $orderBy = null)
 * @method Material[]    findAll()
 * @method Material[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaterialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Material::class);
    }

    public function findBySearch(string $search = '', int $start = 0, int $length = 10, bool $showOutOfStock = false): array
    {
        $qb = $this->createQueryBuilder('m');

        if ($search) {
            $qb->andWhere('LOWER(m.name) LIKE LOWER(:search)')
                ->setParameter('search', '%' . strtolower($search) . '%');
        }

        if ($showOutOfStock) {
            $qb->andWhere('m.quantity >= 0');
        } else {
            $qb->andWhere('m.quantity > 0');
        }

        return $qb
            ->orderBy('m.id', 'ASC')
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->getQuery()
            ->getResult();
    }
}
