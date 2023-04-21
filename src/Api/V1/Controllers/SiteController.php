<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Illuminate\Http\Request;
use Swoole\Http\Server;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function cache()
    {
        include_once app_path('preload.php');
        $current     = realpath_cache_size();
        $value       = ini_get('realpath_cache_size');
        $value       = trim($value);
        $last        = strtolower(substr($value, -1));
        if (in_array($last, ['g', 'm', 'k'], true)) {
            $value = (int) substr($value, 0, -1);

            $value *= match ($last) {
                'g' => 1024 * 1024 * 1024,
                'm' => 1024 * 1024,
                'k' => 1024,
            };
        }
        $ttl         = ini_get('realpath_cache_ttl');
        $percentUsed = $current * 100 / $value;

        return $this->response([
            'current' => $current,
            'max' => $value,
            'percent' => $percentUsed,
            'ttl' => $ttl,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function health(Request $request)
    {
        return $this->response([]);
    }

    /**
     * Return openswoole metrics.
     */
    public function metrics()
    {
        return app(Server::class)->stats(\OPENSWOOLE_STATS_OPENMETRICS);
    }
}
