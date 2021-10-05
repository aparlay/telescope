<?php

namespace Aparlay\Core\Api\V1\Services;

use Aparlay\Core\Jobs\DeleteAvatar;
use Aparlay\Core\Jobs\UploadAvatar;
use Flow\Config;
use Flow\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    public static function chunkUpload(Request $request): array
    {
        $config = new Config(['tempDir' => Storage::disk('upload')]);
        Log::error(Storage::disk('local')->path('chunk'));
        $config->setTempDir(Storage::disk('local')->path('chunk'));

        $result = ['data' => [], 'code' => 200];

        $FILE = [
            'name' => $request->file('file')?->getClientOriginalName(),
            'type' => $request->file('file')?->getType(),
            'tmp_name' => $request->file('file')?->getFilename(),
            'error' => $request->file('file')?->getError(),
            'size' => $request->file('file')?->getSize(),
        ];
        $fileRequest = new \Flow\Request($request->all(), $FILE);
        $file = new File($config, $fileRequest);

        if ($request->isMethod('GET')) {
            $result['code'] = $file->checkChunk() ? 200 : 204;

            return $result;
        }

        if ($file->validateChunk()) {
            if (! $request->hasFile('file') && ! $request->file('file')?->isValid()) {
                abort(400, __('Cannot find uploaded file'));
            }

            $chunkName = $file->getChunkPath($fileRequest->getCurrentChunkNumber());
            if ($request->file->storeAs('chunk', $chunkName, 'local') === false) {
                abort(400, __('Cannot move uploaded file'));
            }
        } else {
            abort(400, __('Invalid chunk uploaded'));
        }

        $fileName = strtolower($request->input('flowFilename'));
        $fileName = uniqid('tmp_', true).'.'.pathinfo($fileName, PATHINFO_EXTENSION);
        Log::error(Storage::disk('upload')->path($fileName));
        if ($file->validateFile() && $file->save(Storage::disk('upload')->path($fileName))) {
            $file->deleteChunks();
            $result['data'] = ['file' => $fileName];
            $result['code'] = 201;

            return $result;
        }

        return $result;
    }
}
