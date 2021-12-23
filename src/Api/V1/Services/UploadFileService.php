<?php

namespace Aparlay\Core\Api\V1\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadFileService
{
    protected $disk;
    protected $filePrefix;
    protected $baseFileName;

    public function __construct($disk = 'upload', $filePrefix = 'waptap_')
    {
        $this->disk = $disk;
    }

    public function upload(UploadedFile $file)
    {
        if (! $file->isValid()) {
            return false;
        }

        $extension = $file->getClientOriginalExtension();
        $this->baseFileName = uniqid($this->filePrefix, false).'.'.$extension;
        $filePath = 'temp/'. $this->baseFileName;
        $wasSaved = Storage::disk($this->disk)->put($filePath, $file->getContent());

        if (!$wasSaved) {
            throw new \ErrorException('Could not upload file, please try again');
        }

        return $filePath;
    }

    /**
     * @return mixed
     */
    public function getBaseFileName()
    {
        return $this->baseFileName;
    }


    /**
     * @return mixed|string
     */
    public function getDisk(): mixed
    {
        return $this->disk;
    }


    /**
     * @param mixed $filePrefix
     */
    public function setFilePrefix($filePrefix): void
    {
        $this->filePrefix = $filePrefix;
    }

    /**
     * @return mixed
     */
    public function getFilePrefix()
    {
        return $this->filePrefix ?? uniqid();
    }

}
