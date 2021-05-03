<?php
namespace App\Service\Tools\FileSystem\Public\Upload;

use App\Service\Tools\FileSystem\FileSystemCrudService;
use App\Service\Tools\FileSystem\FileSystemServiceBase;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class S3PublicUploadService extends FileSystemServiceBase
{
    const FILE_SYSTEM_NAME = "s3_filesystem";

    private FilesystemOperator $s3Filesystem;

    public function __construct(
        FilesystemOperator $s3Filesystem,
        FileSystemCrudService $fileSystemCrudService,
        ParameterBagInterface $parameterBag,
    )
    {
        parent::__construct($fileSystemCrudService, $parameterBag);
        $this->s3Filesystem = $s3Filesystem;
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    public function sendToS3($fileName, $content) {
        try {
            $this->s3Filesystem->writeStream($fileName, $content);
            return $this->checkFileExists($fileName);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    public function checkFileExists(string $fileName) {
        try {
            return $this->s3Filesystem->fileExists($fileName);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    public function readFileFromS3(string $fileName) {
        try {
            return $this->s3Filesystem->readStream($fileName);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    public function deleteFileFromS3(string $fileName) {
        try {
            $this->s3Filesystem->delete($fileName);
            return (!$this->s3Filesystem->fileExists($fileName));
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}