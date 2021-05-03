<?php
namespace App\Service\Tools\FileSystem;

use App\Service\BaseService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileSystemServiceBase extends BaseService
{
    const FILE_DOWNLOAD_ROOT_PATH = "/files/download/file/%s";
    public FileSystemCrudService $fileSystemService;
    protected ParameterBagInterface $parameterBag;

    public function __construct(FileSystemCrudService $fileSystemService, ParameterBagInterface $parameterBag)
    {
        $this->fileSystemService = $fileSystemService;
        $this->parameterBag = $parameterBag;
    }

//    public function getFileDownloadUrl(File $file) {
//        $createFileDownload = $this->fileSystemService->createFileDownload($file);
//        return $this->buildDownloadUrl($createFileDownload);
//    }
//
//    protected function buildDownloadUrl(FileDownload $fileDownload) {
//        return $this->parameterBag->get("app.base_url") . sprintf(
//            self::FILE_DOWNLOAD_ROOT_PATH, $fileDownload->getDownloadKey()
//            );
//    }
}