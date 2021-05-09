<?php

namespace App\Service\Tools\FileSystem\Public\Download;

use App\Service\Tools\FileSystem\FileSystemCrudService;
use App\Service\Tools\FileSystem\FileSystemServiceBase;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LocalPublicDownloadService extends FileSystemServiceBase
{
    const FILE_SYSTEM_NAME = "local_public_filesystem";

    private FilesystemOperator $localPublicFilesystem;
    private string $publicDownloadDir;

    public function __construct(
        FilesystemOperator $localPublicFilesystem,
        FileSystemCrudService $fileSystemCrudService,
        ParameterBagInterface $parameterBag,
        string $projectDir,
        string $publicDir
    )
    {
        parent::__construct($fileSystemCrudService, $parameterBag);
        $this->localPublicFilesystem = $localPublicFilesystem;
        $this->publicDownloadDir = $publicDir . "/downloads";
    }

    public function readFileStream(string $path) {
        $resource = $this->localPublicFilesystem->readStream($path);
        if ($resource === false) {
            throw new BadRequestHttpException(sprintf("Error opening file stream for path: (%s)", $path));
        }
        return $resource;
    }

    public function getDownloadsFilesystem() {
        return $this->localPublicFilesystem;
    }
}