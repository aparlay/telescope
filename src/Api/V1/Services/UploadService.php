<?php

namespace Aparlay\Core\Api\V1\Services;

use Flow\Config;
use Flow\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    public static function chunkUpload(Request $request): array
    {
        $config = new Config(array(
            'tempDir' => '/var/www/aparlay/alua/storage/app/chunk/', //With write access
        ));

        $result = ['data' => [], 'code' => 500];

        $file = new File($config);
        if ($request->isMethod('GET')) {
            $result['code'] = $file->checkChunk() ? 200 : 204;

            return $result;
        }

        if ($file->validateChunk()) {
            $file->saveChunk();
        } else {
            abort(400, __('Invalid chunk uploaded'));
        }

        $fileName = strtolower($request->input('flowFilename'));
        $fileName = uniqid('tmp_', true).'.'.pathinfo($fileName, PATHINFO_EXTENSION);
        if ($file->validateFile() && $file->save('/var/www/aparlay/alua/storage/app/upload/'.$fileName)) {
            $result['data'] = ['file' => $fileName];
            $result['code'] = 201;

            return $result;
        }

        return $result;
    }
}
