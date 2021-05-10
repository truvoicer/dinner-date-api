<?php

namespace App\Service\Tools\FileSystem;

use App\Entity\File;
use App\Entity\FileDownload;
use App\Entity\FileSystem;
use App\Entity\User;
use App\Repository\FileDownloadRepository;
use App\Repository\FileRepository;
use App\Repository\FileSystemRepository;
use App\Service\Tools\HttpRequestService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

class FileSystemCrudService
{

    private EntityManagerInterface $entityManager;
    private HttpRequestService $httpRequestService;
    private FileRepository $fileRepository;
    private FileSystemRepository $fileSystemRepository;
    private FileDownloadRepository $fileDownloadRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        HttpRequestService $httpRequestService
    )
    {
        $this->entityManager = $entityManager;
        $this->httpRequestService = $httpRequestService;
        $this->fileRepository = $this->entityManager->getRepository(File::class);
        $this->fileSystemRepository = $this->entityManager->getRepository(FileSystem::class);
        $this->fileDownloadRepository = $this->entityManager->getRepository(FileDownload::class);
    }

    public function findByQuery(string $query)
    {
        return $this->fileRepository->findByQuery($query);
    }

    public function getFileById(int $fileId) {
        $file = $this->fileRepository->findOneBy(["id" => $fileId]);
        if ($file === null) {
            throw new BadRequestHttpException(sprintf("FileSystem item id:%s not found in database.",
                $fileId
            ));
        }
        return $file;
    }

    private function getFileObject(File $file, FileSystem $fileSystem, User|UserInterface $user, array $data) {
        $file->setMediaCategory($data['media_category']);
        $file->setMediaType($data['media_type']);
        $file->setFilename($data['file_name']);
        $file->setTempPath($data['temp_path']);
        $file->setFileType($data['file_type']);
        $file->setMimeType($data['mime_type']);
        $file->setExtension($data['file_extension']);
        $file->setFileSize($data['file_size']);
        $file->setFileSystem($fileSystem);
        $file->setUser($user);
        return $file;
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function getFileDownloadObject(FileDownload $fileDownload, File $file) {
        $fileDownload->setFile($file);
        $fileDownload->setDownloadKey($this->generateRandomString(16));
        return $fileDownload;
    }

    public function createFileDownload(File $file)
    {
        $fileDownload = $this->getFileDownloadObject(new FileDownload(), $file);
        if ($this->httpRequestService->validateData(
            $fileDownload
        )) {
            return $this->fileDownloadRepository->saveFileDownload($fileDownload);
        }
        return false;
    }

    public function createFile(User|UserInterface $user, array $data)
    {
        $getFileSystem = $this->fileSystemRepository->findOneBy(["name" => $data["file_system"]]);

        if ($getFileSystem === null) {
            $fileSystemObject = $this->fileSystemRepository->getFileSystemObject(new FileSystem(), $data["file_system"], null, null);
            $getFileSystem = $this->fileSystemRepository->saveFileSystem($fileSystemObject);
        }
        $getUserCategoryFile = $this->fileRepository->findOneBy([
            "media_category" => $data["media_category"],
            "user" => $user
        ]);
        if ($getUserCategoryFile === null) {
            $file = $this->getFileObject(new File(), $getFileSystem, $user, $data);
        } else {
            $file = $this->getFileObject($getUserCategoryFile, $getUserCategoryFile->getFileSystem(), $getUserCategoryFile->getUser(), $data);
        }
        if ($this->httpRequestService->validateData(
            $file
        )) {
            return $this->fileRepository->saveFile($file);
        }
        return false;
    }

    public function updateFile(array $data)
    {
        $file = $this->fileRepository->findOneBy(["id" => $data["id"]]);
        if ($file === null) {
            throw new BadRequestHttpException(sprintf("File id:%d not found in database.", $data["id"]));
        }
        if ($this->httpRequestService->validateData(
            $this->getFileObject($file, $file->getFileSystem(), $file->getUser(), $data)
        )) {
            return $this->fileRepository->saveFile($file);
        }
        return false;
    }

    public function findByParams(string $sort, string  $order, int $count) {
        return  $this->fileRepository->findByParams($sort,  $order, $count);
    }

    public function deleteFileById(int $fileId) {
        $file = $this->fileRepository->findOneBy(["id" => $fileId]);
        if ($file === null) {
            throw new BadRequestHttpException(sprintf("File id: %s not found in database.", $fileId));
        }
        return $this->deleteFile($file);
    }
    public function deleteFile(File $file) {
        return $this->fileRepository->deleteFile($file);
    }

    public function deleteFileDownloadById(int $fileDownloadId) {
        $fileDownload = $this->fileRepository->findOneBy(["id" => $fileDownloadId]);
        if ($fileDownload === null) {
            throw new BadRequestHttpException(sprintf("File download id: %s not found in database.", $fileDownloadId));
        }
        return $this->fileDownloadRepository->deleteFileDownload($fileDownload);
    }

    public function deleteFileDownload(FileDownload $fileDownload) {
        if ($fileDownload === null) {
            return false;
        }
        return $this->fileDownloadRepository->deleteFileDownload($fileDownload);
    }

}