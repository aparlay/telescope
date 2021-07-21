<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\Report;
use Aparlay\Core\Api\V1\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use MongoDB\BSON\ObjectId;

class ReportController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  User  $user
     * @param  Request  $request
     * @return Response
     */
    public function user(User $user, Request $request)
    {
        if (Gate::forUser(auth()->user())->denies('report-user', $user)) {
            response()->format([], 'You cannot like this video at the moment.', 403);
        }

        $request->validate([
            'reason' => 'required|max:255',
        ]);

        $model = new Report([
            'reason' => $request->post('reason'),
            'type' => Report::TYPE_MEDIA,
            'status' => Report::STATUS_REPORTED,
            'user_id' => new ObjectId($user->_id),
        ]);
        $model->save();
        auth()->user()->notify(new ReportSent($model));

        return response()->format($model, '', 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Media  $media
     * @param  Request  $request
     * @return Response
     */
    public function media(Media $media, Request $request)
    {
        if (Gate::forUser(auth()->user())->denies('report-media', $media)) {
            response()->format([], 'You cannot like this video at the moment.', 403);
        }

        $request->validate([
            'reason' => 'required|max:255',
        ]);

        $model = new Report([
            'reason' => $request->post('reason'),
            'type' => Report::TYPE_MEDIA,
            'status' => Report::STATUS_REPORTED,
            'media_id' => new ObjectId($media->_id),
        ]);
        $model->save();

        return response()->format($model, '', 201);
    }
}
