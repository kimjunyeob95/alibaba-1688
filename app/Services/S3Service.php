<?php

namespace App\Services;

use App\Abstracts\UploadAbstract;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class S3Service extends UploadAbstract
{
    private string $appEnv;
    private string $appName;
    public function __construct()
    {
        parent::__construct(Storage::disk('s3'));
        $this->appEnv  = ( env("APP_ENV", "local") != "production" ) ? "dev/" : "";
        $this->appName = env("APP_NAME", "1688");
    }

    public function uploadFile(string $originFilePath, string $fileName): ?string
    {
        $response = Http::get($originFilePath); // 외부 URL에서 이미지를 가져옴
        if ($response->successful()) {
            $filePath = $this->appName . "/" . $this->appEnv . date('Y/m/d/') . $fileName . "." . pathinfo($originFilePath, PATHINFO_EXTENSION);
            try {
                Storage::disk('s3')->put($filePath, $response->body()); // 이미지를 S3에 저장
            } catch (Exception $e) {
                return null;
            }
            return $this->getFile($filePath);
        } else {
            return null;
        }
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
        return $this->disk->delete($filePath);
    }

    public function listFiles(string $directoryPath): array
    {
        return $this->disk->files($directoryPath);
    }
}
