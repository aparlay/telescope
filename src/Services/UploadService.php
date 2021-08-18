<?php

namespace Aparlay\Core\Services;

use Flow\Config;
use Flow\File;
use Flow\Request as FlowRequest;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    public static function chunkUpload(): array
    {
        $config = new Config();
        $config->setTempDir(Storage::path('chunk'));
        $code = 500;

        $result = ['data' => [], 'code' => $code];

        $flowRequest = new FlowRequest();

        $fileName = strtolower($flowRequest->getFileName());
        $fileName = uniqid('tmp_', true).'.'.pathinfo($fileName, PATHINFO_EXTENSION);

        $file = new File($config);

        if (request()->isMethod('get')) {
            $result['code'] = $file->checkChunk() ? 200 : 204;
        } elseif ($file->validateChunk()) {
            $file->saveChunk();
        } else {
            abort(400, __('Invalid chunk uploaded'));
        }
        if ($file->validateFile() && $file->save(Storage::path('upload').'/'.$fileName)) {
            $code = 201;
            $result['data'] = ['file' => $fileName];
            $result['code'] = $code;

            return $result;
        }

        return $result;
    }
}
