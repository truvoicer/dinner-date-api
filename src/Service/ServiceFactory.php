<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ServiceFactory
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function getService(string $serviceId) {
        $service = $this->container->get(
            $serviceId,
            ContainerInterface::NULL_ON_INVALID_REFERENCE
        );
        if ($service === null) {
            throw new BadRequestHttpException(sprintf("Invalid service [%s]", $serviceId));
        }
        return $service;
    }
}