<?php

namespace App\Packages;

use App\Abstracts\UploadAbstract;
use Exception;
use Illuminate\Support\Facades\Storage;

class S3 extends UploadAbstract
{
    public function __construct()
    {
        parent::__construct(Storage::disk('s3'));
    }

    public function uploadFile(string $fileName, string $content): bool
    {
        $isUploadFlag = false;
        try {
            $isUploadFlag = $this->disk->put($fileName, $content); // 이미지를 S3에 저장
        } catch (Exception $e) {
            $isUploadFlag = false;
        }

        return $isUploadFlag;
    }

    public function getFile(string $filePath): ?string
    {
        if ($this->disk->exists($filePath)) {
            return $this->disk->get($filePath);
        }
        return null;
    }

    public function deleteFile(string $filePath): bool
    {
        $filterPath = str_replace(env("AWS_URL"), "", $filePath);
        return $this->disk->delete($filterPath);
    }

    public function listFiles(string $directoryPath): array
    {
        return $this->disk->files($directoryPath);
    }
}
