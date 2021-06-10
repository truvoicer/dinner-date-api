<?php

namespace App\Service\User;

use App\Entity\File;
use App\Entity\MediaCollection;
use App\Entity\User;
use App\Entity\UserMediaCollection;
use App\Repository\FileRepository;
use App\Repository\MediaCollectionRepository;
use App\Repository\UserMediaCollectionRepository;
use App\Service\Tools\FileSystem\FileSystemCrudService;
use App\Service\Tools\HttpRequestService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MediaService extends UserService
{
    protected MediaCollectionRepository $mediaCollectionRepository;
    protected UserMediaCollectionRepository $userMediaCollectionRepository;
    protected FileRepository $fileRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        HttpRequestService $httpRequestService,
        TokenStorageInterface $tokenStorage,
    )
    {
        parent::__construct($entityManager, $httpRequestService, $tokenStorage);
        $this->mediaCollectionRepository = $entityManager->getRepository(MediaCollection::class);
        $this->userMediaCollectionRepository = $entityManager->getRepository(UserMediaCollection::class);
        $this->fileRepository = $entityManager->getRepository(File::class);

    }

    public function getUserMediaCollectionsByCollectionName(User|UserInterface $user, string $collection, array $condition = []) {
        return $this->userMediaCollectionRepository->getUserMediaCollectionsByCollectionName($user, $collection, $condition);
    }
    public function getUserMediaCollectionsByCollection(User|UserInterface $user, MediaCollection $collection, array $condition = []) {
        return $this->userMediaCollectionRepository->getUserMediaCollectionsByCollection($user, $collection, $condition);
    }

    public function findMediaCollectionById(int $id) {
        $mediaCollection = $this->mediaCollectionRepository->find($id);
        if ($mediaCollection === null) {
            throw new BadRequestHttpException("Media collection [$id] not found.");
        }
        return $mediaCollection;
    }

    public function findUserMediaCollectionById(int $id) {
        $userMediaCollection = $this->userMediaCollectionRepository->find($id);
        if ($userMediaCollection === null) {
            throw new BadRequestHttpException("User media collection [$id] not found.");
        }
        return $userMediaCollection;
    }

    public function createUserMediaCollection(User|UserInterface $user, ?array $data)
    {
//        $findUserCollection = $this->userMediaCollectionRepository->findByParams(
//            ["name" => UtilsService::stringToSnakeCase($data["name"]), "user" => $user]
//        );
//
//        if ($findUserCollection === null) {
//            throw new BadRequestHttpException(
//                sprintf("Collection [%s] already exists.", UtilsService::stringToSnakeCase($data["name"]))
//            );
//        }

        $userCollectionObject = $this->userMediaCollectionRepository->getUserMediaCollectionObject(
            new UserMediaCollection(),
            $user,
            $data
        );
        if ($this->httpRequestService->validateData($userCollectionObject)) {
            return $this->userMediaCollectionRepository->createUserMediaCollection($userCollectionObject);
        }
        return false;
    }

    public function updateUserMediaCollection(UserMediaCollection $userMediaCollection, User|UserInterface $user, array $data)
    {
        $userCollectionObject = $this->userMediaCollectionRepository->getUserMediaCollectionObject(
            $userMediaCollection,
            $user,
            $data
        );
        if ($this->httpRequestService->validateData($userCollectionObject)) {
            return $this->userMediaCollectionRepository->updateUserMediaCollection($userCollectionObject);
        }
        return false;
    }

    public function addUserMediaCollectionFile(UserMediaCollection $userMediaCollection, User|UserInterface $user, array $data)
    {
        if (!isset($data["files"]) || !is_array($data["files"]) || count($data["files"]) === 0) {
            throw new BadRequestHttpException("Files not in request or invalid.");
        }
        $files = [];
        $files["files"] = array_map(function ($fileId) {
            return $this->fileRepository->find((int)$fileId);
        }, $data["files"]);
        return $this->updateUserMediaCollection($userMediaCollection, $user, $files);
    }

    public function deleteUserMediaCollectionById(int $id)
    {
        return $this->userMediaCollectionRepository->deleteUserMediaCollectionById($id);
    }

    public function deleteUserMediaCollection(UserMediaCollection $userMediaCollection)
    {
        return $this->userMediaCollectionRepository->deleteUserMediaCollection($userMediaCollection);
    }

}