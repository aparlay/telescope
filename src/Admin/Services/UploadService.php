<?php

namespace Aparlay\Core\Admin\Services;

use Flow\Config;
use Flow\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    public static function chunkUpload(Request $request): array
    {
        $config          = new Config(['tempDir' => Storage::disk('upload')]);
        $chunkPath       = Storage::disk('local')->path('chunk');
        $config->setTempDir($chunkPath);

        $result          = ['data' => [], 'code' => 200];

        $fileArray       = [
            'name' => $request->file('file')?->getClientOriginalName(),
            'type' => $request->file('file')?->getType(),
            'tmp_name' => $request->file('file')?->getFilename(),
            'error' => $request->file('file')?->getError(),
            'size' => $request->file('file')?->getSize(),
        ];
        $fileRequest     = new \Flow\Request($request->all(), $fileArray);
        $file            = new File($config, $fileRequest);

        if ($request->isMethod('GET')) {
            $result['code'] = $file->checkChunk() ? 200 : 204;

            return $result;
        }

        if ($file->validateChunk()) {
            if (!$request->hasFile('file') && !$request->file('file')?->isValid()) {
                abort(400, __('Cannot find uploaded file'));
            }

            $chunkName = str_replace($chunkPath, '', $file->getChunkPath($fileRequest->getCurrentChunkNumber()));
            if ($request->file->storeAs('chunk', $chunkName, 'local') === false) {
                abort(400, __('Cannot move uploaded file'));
            }
        } else {
            abort(400, __('Invalid chunk uploaded'));
        }

        $fileName        = uniqid('tmp_', true) . '.' . $request->file->getClientOriginalExtension();
        $destinationPath = Storage::disk('upload')->path($fileName);
        if ($file->validateFile() && $file->save($destinationPath)) {
            $file->deleteChunks();
            $result['data'] = [
                'code' => 201,
                'status' => 'OK',
                'data' => ['file' => $fileName],
            ];
            $result['code'] = 201;

            return $result;
        }

        return $result;
    }
}
