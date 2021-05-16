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
    const FILE_SYSTEM_NAME = "local_temp_filesystem";

    private FilesystemOperator $localTempFilesystem;
    private string $tempDir;

    public function __construct(
        FilesystemOperator $localTempFilesystem,
        FileSystemCrudService $fileSystemCrudService,
        ParameterBagInterface $parameterBag,
        string $projectDir,
        string $tempDir,
    )
    {
        parent::__construct($fileSystemCrudService, $parameterBag);
        $this->localTempFilesystem = $localTempFilesystem;
        $this->tempDir = $tempDir;
    }

    public function moveToTempDir(UploadedFile $uploadedFile, string $filePath): array|bool
    {
        $filesystem = new Filesystem();
        $tempPath =  $this->tempDir . $filePath;
        try {
            $filesystem->copy($uploadedFile->getRealPath(), $tempPath);
            if (!$this->localTempFilesystem->fileExists($filePath)) {
                return false;
            }
            return $tempPath;
        } catch (IOExceptionInterface $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        } catch (FilesystemException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    public function readTempFile($filePath): string
    {
        return $this->localTempFilesystem->read($filePath);
    }

    public function readFileStreamFromTemp(string $path) {
        $resource = $this->localTempFilesystem->readStream($path);
        if ($resource === false) {
            throw new BadRequestHttpException(sprintf("Error opening file stream for path: (%s)", $path));
        }
        return $resource;
    }

    public function checkFileExists(string $fileName): bool
    {
        try {
            return $this->localTempFilesystem->fileExists($fileName);
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
            $this->localTempFilesystem->delete($path);
            return $this->checkFileExists($path);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}