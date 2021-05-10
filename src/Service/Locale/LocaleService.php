<?php

namespace App\Service\Locale;

use App\Entity\Country;
use App\Repository\CountryRepository;
use App\Service\BaseService;
use App\Service\Tools\HttpRequestService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LocaleService extends CountryService
{

    public function __construct(EntityManagerInterface $entityManager, HttpRequestService $httpRequestService)
    {
        parent::__construct($entityManager, $httpRequestService);
    }

}