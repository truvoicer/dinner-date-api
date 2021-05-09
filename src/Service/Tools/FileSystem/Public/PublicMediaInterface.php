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
        $this->requestData = HttpRequestService::getRequestData($this->request);
        $this->s3PublicUploadService = $s3PublicUploadService;
        $this->localPublicDownloadService = $localPublicDownloadService;
        $this->localPublicUploadService = $localPublicUploadService;
        $this->localTempUploadService = $localTempUploadService;
        $this->fileSystemCrudService = $fileSystemCrudService;
    }

    public function mediaUploadHandler() {
        switch ($this->requestData["media_category"]) {
            case "profile_cover":
            case "profile_pic":
                return $this->profileImageUploadHandler();
        }
    }

    public function mediaDeleteHandler() {
        switch ($this->requestData["media_category"]) {
            case "profile_cover":
            case "profile_pic":
                return $this->profileImageDeleteHandler();
        }
    }

    protected function profileImageDeleteHandler() {
        $file = $this->fileSystemCrudService->getFileById($this->requestData["file_id"]);
        if(!$this->s3PublicUploadService->deleteFileFromS3($file->getFilename() . $file->getExtension())) {
            return false;
        }
        return $this->fileSystemCrudService->deleteFile($file);
    }

    protected function profileImageUploadHandler() {
        $file = $this->request->files->get("file");
        if (!self::validateImageFile($file)) {
            return false;
        }
        $getPath =  $this->localTempUploadService->moveToTempDir(
            $file,
            $this->buildFileName($this->requestData["media_category"]),
            self::ALLOWED_IMAGE_MIME_TYPES[$file->getClientMimeType()]
        );
        $fileName = $getPath["filename"] . $getPath["ext"];
        $content = $this->localTempUploadService->readFileStreamFromTemp($getPath["full_filename"]);

        if(!$this->s3PublicUploadService->sendToS3($fileName, $content)) {
            return false;
        }

        if (!$this->localTempUploadService->deleteFileFromTemp($getPath["full_filename"])) {

        }
        return $this->fileSystemCrudService->createFile($this->getUser(), [
            'media_category' => $this->requestData["media_category"],
            'media_type' => "image",
            'file_name' => $getPath["filename"],
            'file_url' => $getPath["full_filename"],
            'temp_path' => $getPath["path"],
            'file_type' => ltrim($getPath["ext"], "."),
            'mime_type' => $getPath["mime_type"],
            'file_extension' => $getPath["ext"],
            'file_size' => $getPath["file_size"],
            'file_system' => S3PublicUploadService::FILE_SYSTEM_NAME,
        ]);
    }

    public static function validateImageFile(\Symfony\Component\HttpFoundation\File\UploadedFile $file) {
        return array_key_exists($file->getClientMimeType(), self::ALLOWED_IMAGE_MIME_TYPES);
    }

    private function buildFileName(string $mediaCategory) {
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
}