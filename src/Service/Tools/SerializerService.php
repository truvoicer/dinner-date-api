<?php

namespace App\Service\Tools;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

class SerializerService
{

    private $classMetadataFactory;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
    }

    public function entityToArray($entity, array $groups = ['main'])
    {
        $normalizer = new ObjectNormalizer($this->classMetadataFactory, new CamelCaseToSnakeCaseNameConverter(), null,
            null, null, null, $this->getDefaultContext());
        $serializer = new Serializer([$normalizer]);
        return $serializer->normalize($entity, null,
            [
                "groups" => $groups
            ]);
    }

    public function entityArrayToArray(array $entityArray, array $groups = ['main'])
    {
        return array_map(function ($item) use ($groups) {
            return $this->entityToArray($item, $groups);
        }, $entityArray);
    }

    private function getDefaultContext()
    {
        $dateCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $innerObject instanceof \DateTime ? $innerObject->format("Y-m-d H:i:s") : '';
        };
        return [
            ObjectNormalizer::CALLBACKS => [
                'date_updated' => $dateCallback,
                'date_created' => $dateCallback,
            ],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object;
            },
        ];
    }
}