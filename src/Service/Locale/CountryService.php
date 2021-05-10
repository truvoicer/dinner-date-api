<?php

namespace App\Service\Locale;

use App\Entity\Country;
use App\Repository\CountryRepository;
use App\Service\BaseService;
use App\Service\Tools\HttpRequestService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CountryService extends BaseService
{

    protected EntityManagerInterface $em;
    protected HttpRequestService $httpRequestService;
    protected CountryRepository $countryRepository;

    public function __construct(EntityManagerInterface $entityManager, HttpRequestService $httpRequestService)
    {
        $this->em = $entityManager;
        $this->httpRequestService = $httpRequestService;
        $this->countryRepository = $this->em->getRepository(Country::class);
    }

    public function getCountryList(array $conditions = []) {
        return $this->countryRepository->findByParams($conditions);
    }

    public function createCountry(string $name, string $alpha2, string $alpha3)
    {
        $country = $this->countryRepository->getCountryObject(new Country(), $name, $alpha2, $alpha3);
        if ($this->httpRequestService->validateData($country)) {
            return $this->countryRepository->saveCountry($country);
        }
        return false;
    }

    public function updateCountry(Country $country, array $data)
    {
        if (!isset($data["name"])) {
            throw new BadRequestHttpException("Invalid country name");
        }
        if (!isset($data["alpha_2"])) {
            throw new BadRequestHttpException("Invalid alpha_2 value");
        }
        if (!isset($data["alpha_3"])) {
            throw new BadRequestHttpException("Invalid alpha_3 value");
        }
        $getCountry = $this->countryRepository->getCountryObject($country, $data["name"], $data["alpha_2"], $data["alpha_3"]);
        if ($this->httpRequestService->validateData($getCountry)) {
            return $this->countryRepository->saveCountry($getCountry);
        }
        return false;
    }

    public function deleteCountryById(int $countryId)
    {
        $country = $this->countryRepository->find($countryId);
        if ($country === null) {
            throw new BadRequestHttpException(sprintf("Country id: %s not found in database.", $countryId));
        }
        return $this->deleteCountry($country);
    }

    public function deleteCountry(Country $country)
    {
        return $this->countryRepository->deleteCountry($country);
    }
}