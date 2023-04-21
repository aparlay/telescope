<?php

namespace Aparlay\Core\Api\V1\Services;

use Flow\Config;
use Flow\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UploadService
{
    /**
     * @throws \Flow\FileLockException
     * @throws \Flow\FileOpenException
     */
    public static function split(Request $request): array
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
            $hash           = md5_file($destinationPath);
            $result['data'] = [
                'code' => 201,
                'status' => 'OK',
                'data' => [
                    'file' => $fileName,
                    'hash' => $hash,
                ],
            ];
            $result['code'] = 201;

            return $result;
        }

        return $result;
    }

    public static function stream(Request $request): array
    {
        $result = ['data' => [], 'code' => 200];
        if ($request->file('file')) {
            $file            = $request->file('file');
            $fileName        = uniqid('tmp_', true) . '.' . $file->getClientOriginalExtension();
            $destinationPath = Storage::disk('upload')->path('/');
            $file->move($destinationPath, $fileName);
            $hash            = md5_file($destinationPath . '/' . $fileName);

            if (!Storage::disk('upload')->exists($fileName)) {
                throw new UnprocessableEntityHttpException('Cannot upload the file.');
            }
            $result['data']  = [
                'code' => 201,
                'status' => 'OK',
                'data' => [
                    'file' => $fileName,
                    'hash' => $hash,
                ],
            ];
            $result['code']  = 201;

            return $result;
        }

        return $result;
    }
}
