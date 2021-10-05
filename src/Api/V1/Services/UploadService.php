<?php

namespace Aparlay\Core\Api\V1\Services;

use Flow\Config;
use Flow\File;
use Flow\Request as FlowRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    public static function chunkUpload(Request $request): array
    {
        $config = new Config();
        $config->setTempDir(sys_get_temp_dir());
        $code = 500;

        $result = ['data' => [], 'code' => $code];

        $flowRequest = new FlowRequest($request->all(), $request->file('file'));
        $fileName = strtolower($flowRequest->getFileName());
        $fileName = uniqid('tmp_', true).'.'.pathinfo($fileName, PATHINFO_EXTENSION);

        $file = new File($config);
        if ($request->isMethod('GET')) {
            $result['code'] = $file->checkChunk() ? 200 : 204;

            return $result;
        } elseif ($file->validateChunk()) {
            $file->saveChunk();
        } else {
            abort(400, __('Invalid chunk uploaded'));
        }

        if ($file->validateFile() && $file->save('/var/www/aparlay/alua/storage/app/upload/'.$fileName)) {
            $result['data'] = ['file' => $fileName];
            $result['code'] = 201;

            return $result;
        }

        return $result;
    }
}
