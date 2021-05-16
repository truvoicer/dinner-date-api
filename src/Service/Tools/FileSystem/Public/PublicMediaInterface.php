<?php

namespace App\Service\Tools\FileSystem\Public;

use App\Entity\User;
use App\Service\Tools\FileSystem\FileSystemCrudService;
use App\Service\Tools\FileSystem\Public\Download\LocalPublicDownloadService;
use App\Service\Tools\FileSystem\Public\Upload\LocalPublicUploadService;
use App\Service\Tools\FileSystem\Public\Upload\LocalTempUploadService;
use App\Service\Tools\FileSystem\Public\Upload\S3PublicUploadService;
use App\Service\Tools\HttpRequestService;
use GuzzleHttp\Psr7\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

class PublicMediaInterface
{
    const ALLOWED_IMAGE_MIME_TYPES = [
        "image/png" => ".png",
        "image/jpg" => ".jpg",
        "image/jpeg" => ".jpeg",
    ];
    protected User|UserInterface $user;
    protected ?Request $request;
    protected array $requestData;
    protected S3PublicUploadService $s3PublicUploadService;
    protected LocalPublicDownloadService $localPublicDownloadService;
    protected LocalPublicUploadService $localPublicUploadService;
    protected LocalTempUploadService $localTempUploadService;
    protected FileSystemCrudService $fileSystemCrudService;
    private bool $overwrite = false;

    public function __construct(
        RequestStack $request,
        S3PublicUploadService $s3PublicUploadService,
        LocalPublicDownloadService $localPublicDownloadService,
        LocalPublicUploadService $localPublicUploadService,
        LocalTempUploadService $localTempUploadService,
        FileSystemCrudService $fileSystemCrudService,
    )
    {
        $this->request = $request->getCurrentRequest();
        $this->requestData = HttpRequestService::getRequestData($this->request, true);
        $this->s3PublicUploadService = $s3PublicUploadService;
        $this->localPublicDownloadService = $localPublicDownloadService;
        $this->localPublicUploadService = $localPublicUploadService;
        $this->localTempUploadService = $localTempUploadService;
        $this->fileSystemCrudService = $fileSystemCrudService;
    }

    public function mediaFetchHandler(array $conditions = [])
    {
        return $this->fileSystemCrudService->findUserFilesByMediaCategory(
            $this->user,
            $this->requestData["media_category"],
            $this->request->query->all()
        );
    }

    public function mediaUploadHandler()
    {
        switch ($this->requestData["media_category"]) {
            case "profile_cover":
            case "profile_pic":
                $this->overwrite = true;
                return $this->s3PublicMediaImageUploadHandler();
            case "media_photo":
                $this->overwrite = false;
                return $this->s3PublicMediaImageUploadHandler();
        }
    }

    public function mediaDeleteHandler()
    {
        switch ($this->requestData["media_category"]) {
            case "profile_cover":
            case "profile_pic":
            case "media_photo":
                return $this->s3PublicMediaDeleteHandler();
        }
    }

    protected function s3PublicMediaDeleteHandler()
    {
        $file = $this->fileSystemCrudService->getFileById($this->requestData["file_id"]);
        if (!$this->s3PublicUploadService->deleteFileFromS3($file->getPath())) {
            return false;
        }
        return $this->fileSystemCrudService->deleteFile($file);
    }

    protected function s3PublicMediaImageUploadHandler()
    {
        return $this->s3PublicMediaUploadHandler(
            "image",
            self::ALLOWED_IMAGE_MIME_TYPES
        );
    }

    protected function s3PublicMediaUploadHandler(string $mediaType, array $allowedMimeTypes = [])
    {
        $uploadedFile = $this->request->files->get("file");
        if (!self::validateImageFile($uploadedFile)) {
            return false;
        }
        $mimeType = $uploadedFile->getClientMimeType();
        if (!array_key_exists($mimeType, $allowedMimeTypes)) {
            throw new BadRequestHttpException(
                sprintf(
                    "Uploaded file mime type [%s] is not allowed for media [%s]",
                    $mimeType, $mediaType
                )
            );
        }

        $ext = $allowedMimeTypes[$mimeType];
        $category = $this->requestData["media_category"];
        $fileName = $this->buildFileName($category);
        $filePath = "/" . $this->user->getId() . "/" . $category . "/" . $fileName . $ext;

        $getTempPath = $this->localTempUploadService->moveToTempDir($uploadedFile, $filePath);
        if (!$getTempPath) {
            throw new BadRequestHttpException("Error processing file upload");
        }

        $content = $this->localTempUploadService->readFileStreamFromTemp($filePath);
        if (!$this->s3PublicUploadService->sendToS3($filePath, $content)) {
            return false;
        }

        $this->localTempUploadService->deleteFileFromTemp($filePath);

        return $this->fileSystemCrudService->createFile(
            $this->getUser(),
            [
                'media_category' => $category,
                'media_type' => $mediaType,
                'file_name' => $fileName,
                'path' => $filePath,
                'file_type' => ltrim($ext, "."),
                'mime_type' => $uploadedFile->getMimeType(),
                'file_extension' => $ext,
                'file_size' => $uploadedFile->getSize(),
                'file_system' => S3PublicUploadService::FILE_SYSTEM_NAME,
            ],
            $this->overwrite
        );

    }

    public static function validateImageFile(\Symfony\Component\HttpFoundation\File\UploadedFile $file)
    {
        return array_key_exists($file->getClientMimeType(), self::ALLOWED_IMAGE_MIME_TYPES);
    }

    private function buildFileName(string $mediaCategory)
    {
        $date = new \DateTime();
        return sprintf(
            "%d_%s_%s",
            $this->getUser()->getId(),
            $mediaCategory,
            $date->format("YmdHis")
        );
    }

    /**
     * @return User|UserInterface
     */
    public function getUser(): UserInterface|User
    {
        return $this->user;
    }

    /**
     * @param User|UserInterface $user
     */
    public function setUser(UserInterface|User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return FileSystemCrudService
     */
    public function getFileSystemCrudService(): FileSystemCrudService
    {
        return $this->fileSystemCrudService;
    }
}