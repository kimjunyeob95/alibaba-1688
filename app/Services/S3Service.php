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
        $response = Http::get($originFilePath);  // 외부 URL에서 이미지를 가져옴
        $result   = null;
        if ($response->successful()) {
            $filePath = $this->appName . "/" . $this->appEnv . date('Y/m/d/') . $fileName . "." . pathinfo($originFilePath, PATHINFO_EXTENSION);
            try {
                $result = $this->disk->put($filePath, $response->body()); // 이미지를 S3에 저장
                if( $result === true ){
                    $result = $this->disk->url($filePath);
                }
            } catch (Exception $e) {
            }
        }

        return $result;
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
