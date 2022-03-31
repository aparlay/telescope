<?php

namespace Aparlay\Core\Api\V1\Traits;

use Aparlay\Core\Constants\StorageType;
use Illuminate\Support\Facades\Storage;

trait HasFileTrait
{
    public function temporaryUrl($duration = 10)
    {
        if (! $this->getFilePath()) {
            return '';
        }

        $validTo = now()->addMinutes($duration);

        if (\App::environment('development')) {
            $validTo = now()->addDays($duration);
        }

        return Storage::disk($this->getStorageDisk())->temporaryUrl($this->getFilePath(), $validTo);
    }

    public function getFilePath()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getStorageDisk()
    {
        return StorageType::S3;
    }
}
