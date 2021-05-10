<?php

namespace App\Repository;

use App\Entity\Country;
use App\Repository\Helpers\RepositoryHelpers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Country|null find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null findOneBy(array $criteria, array $orderBy = null)
 * @method Country[]    findAll()
 * @method Country[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    public function findByParams(array $conditions = [])
    {
        return RepositoryHelpers::addQueryBuilderConditions(
            $this->createQueryBuilder('country'), $conditions
        )
            ->getQuery()
            ->getResult();
    }

    public function getCountryObject(Country $country, string $name, string $alpha2, string $alpha3)
    {
        if (isset($name)) {
            $country->setName($name);
        }
        if (isset($alpha2)) {
            $country->setAlpha2($alpha2);
        }
        if (isset($alpha3)) {
            $country->setAlpha3($alpha3);
        }
        return $country;
    }

    public function saveCountry(Country $country)
    {
        $this->getEntityManager()->persist($country);
        $this->getEntityManager()->flush();
        return $country;
    }

    public function deleteCountry(Country $country)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($country);
        $entityManager->flush();
        return true;
    }
}
