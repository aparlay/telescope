<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Constants\StorageType;
use ErrorException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UploadFileService
{
    protected $disk;
    protected $filePrefix;
    protected $baseFileName;
    protected $mime;
    protected $size;
    protected $md5;

    public function __construct(string $disk = StorageType::UPLOAD)
    {
        $this->disk = $disk;
    }

    public function upload(UploadedFile $file, $path = 'temp')
    {
        if (!$file->isValid()) {
            return false;
        }
        $extension          = $file->getClientOriginalExtension();
        $this->baseFileName = uniqid($this->filePrefix, false) . '.' . $extension;
        $filePath           = $path . DIRECTORY_SEPARATOR . $this->baseFileName;

        $wasSaved           = Storage::disk($this->disk)->put($filePath, $file->getContent());
        $this->md5          = File::hash(Storage::disk($this->disk)->path($filePath));

        $this->mime         = $file->getClientMimeType();
        $this->size         = $file->getSize();

        if (!$wasSaved) {
            throw new ErrorException('Could not upload file, please try again');
        }

        return $filePath;
    }

    /**
     * @return mixed
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return mixed
     */
    public function getMd5()
    {
        return $this->md5;
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
