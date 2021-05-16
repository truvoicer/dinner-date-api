<?php
namespace App\Service\Tools\FileSystem\Public\Upload;

use App\Service\Tools\FileSystem\FileSystemCrudService;
use App\Service\Tools\FileSystem\FileSystemServiceBase;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class S3PublicUploadService extends FileSystemServiceBase
{
    const FILE_SYSTEM_NAME = "s3_public_media_filesystem";

    private FilesystemOperator $s3PublicMediaFilesystem;

    public function __construct(
        FilesystemOperator $s3PublicMediaFilesystem,
        FileSystemCrudService $fileSystemCrudService,
        ParameterBagInterface $parameterBag,
    )
    {
        parent::__construct($fileSystemCrudService, $parameterBag);
        $this->s3PublicMediaFilesystem = $s3PublicMediaFilesystem;
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    public function sendToS3(string $destPath, $contents) {
        try {
            $this->s3PublicMediaFilesystem->writeStream($destPath, $contents, [
                "visibility" => "public"
            ]);
            return $this->checkFileExists($destPath);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    public function checkFileExists(string $fileName) {
        try {
            return $this->s3PublicMediaFilesystem->fileExists($fileName);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch (FilesystemException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    public function readFileFromS3(string $fileName) {
        try {
            return $this->s3PublicMediaFilesystem->readStream($fileName);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch (FilesystemException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    public function deleteFileFromS3(string $path) {
        try {
            $this->s3PublicMediaFilesystem->delete($path);
            return (!$this->s3PublicMediaFilesystem->fileExists($path));
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch (FilesystemException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}