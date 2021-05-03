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

class LocalPublicUploadService extends FileSystemServiceBase
{
    const FILE_SYSTEM_NAME = "public_filesystem";

    private FilesystemOperator $publicFilesystem;
    private string $publicUploadDir;

    public function __construct(
        FilesystemOperator $publicFilesystem,
        FileSystemCrudService $fileSystemCrudService,
        ParameterBagInterface $parameterBag,
        string $projectDir,
        string $publicDir,
    )
    {
        parent::__construct($fileSystemCrudService, $parameterBag);
        $this->publicFilesystem = $publicFilesystem;
        $this->publicUploadDir = $publicDir . "/uploads";
    }

    public function moveToPublicTempDir(UploadedFile $uploadedFile, string $fileName, string $ext) {
        $filesystem = new Filesystem();
        $copyToPath = $this->publicUploadDir . "/" . $fileName . $ext;
        try {
            $filesystem->copy($uploadedFile->getRealPath(), $copyToPath);
            return [
                "path" => $copyToPath,
                "filename" => $fileName,
                "ext" => $ext
            ];
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your directory at ".$exception->getPath();
            return false;
        }
    }

    public function saveUploadTempFileToDatabase(string $fileName, string $fileType, string $ext ) {
//        $saveToDatabase = $this->fileSystemService->createFile([
//            "file_name" => $fileName,
//            "file_path" => $fileName,
//            "file_type" => $fileType,
//            "file_extension" => $ext,
//            "mime_type" => $this->uploadTempFilesystem->getMimetype( $fileName ),
//            "file_size" => $this->uploadTempFilesystem->getSize( $fileName ),
//            "file_system" => self::FILE_SYSTEM_NAME,
//        ]);
//        if (!$saveToDatabase || $saveToDatabase === null) {
//            return false;
//        }
//        return $saveToDatabase;
    }

    public function readTempFile($filePath) {
        return $this->publicFilesystem->read($filePath);
    }

    public function readFileStreamFromTemp(string $path) {
        $resource = $this->publicFilesystem->readStream($path);
        if ($resource === false) {
            throw new BadRequestHttpException(sprintf("Error opening file stream for path: (%s)", $path));
        }
        return $resource;
    }

    public function readFileStreamFromPublic(string $path) {
        $resource = $this->publicFilesystem->readStream($path);
        if ($resource === false) {
            throw new BadRequestHttpException(sprintf("Error opening file stream for path: (%s)", $path));
        }
        return $resource;
    }
}