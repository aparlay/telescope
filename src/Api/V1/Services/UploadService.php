<?php

namespace Aparlay\Core\Api\V1\Services;

use Flow\Config;
use Flow\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    public static function chunkUpload(Request $request): array
    {
        $config = new Config([
            'tempDir' => '/var/www/aparlay/alua/storage/app/chunk/', //With write access
        ]);

        $result = ['data' => [], 'code' => 400];

        $requestFile = [
            'name' => $request->file('file')?->getClientOriginalName(),
            'type' => $request->file('file')?->getType(),
            'tmp_name' => $request->file('file')?->getFilename(),
            'error' => $request->file('file')?->getError(),
            'size' => $request->file('file')?->getSize(),
        ];
        $file = new File($config, new \Flow\Request($request->all(), $requestFile));

        if ($request->isMethod('GET')) {
            $result['code'] = $file->checkChunk() ? 200 : 204;

            return $result;
        }

        if ($file->validateChunk()) {
            if (!$file->saveChunk()) {
                abort(400, __('Cannot move uploaded file'));
            }
        } else {
            abort(400, __('Invalid chunk uploaded'));
        }

        $fileName = strtolower($request->input('flowFilename'));
        $fileName = uniqid('tmp_', true).'.'.pathinfo($fileName, PATHINFO_EXTENSION);
        if ($file->validateFile() && $file->save('/var/www/aparlay/alua/storage/app/upload/'.$fileName)) {
            $file->deleteChunks();
            $result['data'] = ['file' => $fileName];
            $result['code'] = 201;

            return $result;
        }

        return $result;
    }
}
