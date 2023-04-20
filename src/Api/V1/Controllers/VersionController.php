<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Version;
use Illuminate\Http\Response;

class VersionController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(string $os, string $version): Response
    {
        $models             = Version::query()
            ->os($os)
            ->app('waptap')
            ->latest()
            ->get();

        if (empty($models)) {
            return $this->error('Record not found.', [], Response::HTTP_NOT_FOUND);
        }

        $requireForceUpdate = false;
        foreach ($models as $model) {
            if ($model['is_force_update']) {
                $compareResult = version_compare($version, $model['version']);
                if (-1 === $compareResult) {
                    $requireForceUpdate = true;
                }
            }
        }

        return $this->response([
            'require_force_update' => $requireForceUpdate,
            'version' => $models[0],
        ]);
    }
}
