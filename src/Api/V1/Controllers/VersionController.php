<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Models\Media;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param string $os
     * @param string $version
     * @return \Illuminate\Http\Response
     */
    public function show(string $os, string $version)
    {
        //
    }
}
