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

class LocalTempUploadService extends FileSystemServiceBase
{
    const FILE_SYSTEM_NAME = "public_filesystem";

    private FilesystemOperator $tempFilesystem;
    private string $tempDir;

    public function __construct(
        FilesystemOperator $tempFilesystem,
        FileSystemCrudService $fileSystemCrudService,
        ParameterBagInterface $parameterBag,
        string $projectDir,
        string $tempDir,
    )
    {
        parent::__construct($fileSystemCrudService, $parameterBag);
        $this->tempFilesystem = $tempFilesystem;
        $this->tempDir = $tempDir;
    }

    public function moveToTempDir(UploadedFile $uploadedFile, string $fileName, string $ext): array|bool
    {
        $filesystem = new Filesystem();
        $copyToPath = $this->tempDir . "/" . $fileName . $ext;
        try {
            $filesystem->copy($uploadedFile->getRealPath(), $copyToPath);
            return [
                "path" => $copyToPath,
                "filename" => $fileName,
                "full_filename" => $fileName . $ext,
                "ext" => $ext,
                "mime_type" => $uploadedFile->getMimeType(),
                "file_size" => $uploadedFile->getSize(),
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

    public function readTempFile($filePath): string
    {
        return $this->tempFilesystem->read($filePath);
    }

    public function readFileStreamFromTemp(string $path) {
        $resource = $this->tempFilesystem->readStream($path);
        if ($resource === false) {
            throw new BadRequestHttpException(sprintf("Error opening file stream for path: (%s)", $path));
        }
        return $resource;
    }

    public function checkFileExists(string $fileName): bool
    {
        try {
            return $this->tempFilesystem->fileExists($fileName);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch (FilesystemException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    public function deleteFileFromTemp(string $path): bool
    {
        try {
            $this->tempFilesystem->delete($path);
            return $this->checkFileExists($path);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}