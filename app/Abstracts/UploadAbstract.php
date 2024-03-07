<?php

namespace App\Abstracts;

use Illuminate\Contracts\Filesystem\Filesystem;

abstract class UploadAbstract
{
    protected array $returnMsg;
    protected Filesystem $disk;

    public function __construct(Filesystem $disk)
    {
        $this->returnMsg = helpers_fail_message();
        $this->disk = $disk;
    }
    
    /**
     * @func getFile
     * @description '파일 url 반환'
     */
    abstract function getFile(string $filePath): ?string;

    /**
     * @func uploadFile
     * @description '파일 업로드 후 url 반환'
     */
    abstract function uploadFile(string $originFilePath, string $fileName): ?string;

    /**
     * @func deleteFile
     * @description '파일 삭제'
     */
    abstract function deleteFile(string $filePath): bool;

}
