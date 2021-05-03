<?php

namespace App\Service\Tools\FileSystem\Public\Download;

use App\Service\Tools\FileSystem\FileSystemCrudService;
use App\Service\Tools\FileSystem\FileSystemServiceBase;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LocalPublicDownloadService extends FileSystemServiceBase
{
    const FILE_SYSTEM_NAME = "public_filesystem";

    private FilesystemOperator $publicFilesystem;
    private string $publicDownloadDir;

    public function __construct(
        FilesystemOperator $publicFilesystem,
        FileSystemCrudService $fileSystemCrudService,
        ParameterBagInterface $parameterBag,
        string $projectDir,
        string $publicDir
    )
    {
        parent::__construct($fileSystemCrudService, $parameterBag);
        $this->publicFilesystem = $publicFilesystem;
        $this->publicDownloadDir = $publicDir . "/downloads";
    }

    public function readFileStream(string $path) {
        $resource = $this->publicFilesystem->readStream($path);
        if ($resource === false) {
            throw new BadRequestHttpException(sprintf("Error opening file stream for path: (%s)", $path));
        }
        return $resource;
    }

    public function getDownloadsFilesystem() {
        return $this->publicFilesystem;
    }
}