<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Api\V1\Traits\HasUserTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadFileService
{
    use HasUserTrait;

    protected $disk;
    protected $filePrefix;

    public function __construct($disk = 'upload', $filePrefix = 'waptap_')
    {
        $this->disk = $disk;
        $this->filePrefix = $filePrefix;
    }

    public function upload(UploadedFile $file)
    {
        if (! $file->isValid()) {
            return false;
        }
        $extension = $file->getClientOriginalExtension();
        $fileName = uniqid($this->filePrefix, false).'.'.$extension;
        $filePath = 'temp/'.$fileName;
        $wasSaved = Storage::disk($this->disk)->put($filePath, $file->getContent());

        if (! $wasSaved) {
            throw new \ErrorException('Could not upload file, please try again');
        }

        return $filePath;
    }

    /**
     * @return mixed|string
     */
    public function getDisk(): mixed
    {
        return $this->disk;
    }
}
