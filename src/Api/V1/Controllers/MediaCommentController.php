<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Models\MediaComment;
use Aparlay\Core\Api\V1\Requests\MediaCommentRequest;
use Aparlay\Core\Api\V1\Resources\MediaCommentCollection;
use Aparlay\Core\Api\V1\Resources\MediaCommentResource;
use Aparlay\Core\Api\V1\Services\MediaCommentLikeService;
use Aparlay\Core\Api\V1\Services\MediaCommentService;
use Illuminate\Http\Response;

class MediaCommentController extends Controller
{
    public function __construct(
        private MediaCommentService $mediaCommentService,
        private MediaCommentLikeService $mediaCommentLikeService
    ) {
    }

    /**
     * @param Media $media
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function list(Media $media)
    {
        $this->authorize('view', [MediaComment::class, $media]);
        $response = $this->mediaCommentService->list($media);

        return $this->response(new MediaCommentCollection($response), '', );
    }

    /**
     * @param MediaComment $mediaComment
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function like(MediaComment $mediaComment)
    {
        if (auth()->check()) {
            $this->mediaCommentLikeService->setUser(auth()->user());
        }

        $this->mediaCommentLikeService->like($mediaComment);
        $mediaCommentResource = (new MediaCommentResource($mediaComment));

        return $this->response($mediaCommentResource, '', );
    }

    /**
     * @param MediaComment $mediaComment
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function unlike(MediaComment $mediaComment)
    {
        if (auth()->check()) {
            $this->mediaCommentLikeService->setUser(auth()->user());
        }

        $this->mediaCommentLikeService->unlike($mediaComment);
        $mediaCommentResource = (new MediaCommentResource($mediaComment));

        return $this->response($mediaCommentResource, '', );
    }

    public function listReplies(MediaComment $mediaComment)
    {
        $this->authorize('view', [MediaComment::class, $mediaComment->mediaObj]);
        $response = $this->mediaCommentService->listReplies($mediaComment);

        return $this->response(new MediaCommentCollection($response), '', );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MediaCommentRequest $request, Media $media): Response
    {
        $this->authorize('create', [MediaComment::class, $media]);

        if (auth()->check()) {
            $this->mediaCommentService->setUser(auth()->user());
        }

        $text = $request->input('text');
        $response = $this->mediaCommentService->create($media, $text);

        return $this->response(new MediaCommentResource($response), '', 201);
    }

    /**
     * @param MediaCommentRequest $request
     * @param MediaComment $mediaComment
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function reply(MediaCommentRequest $request, MediaComment $mediaComment): Response
    {
        $this->authorize('create', [MediaComment::class, $mediaComment->mediaObj]);

        if (auth()->check()) {
            $this->mediaCommentService->setUser(auth()->user());
        }

        $text = $request->input('text');
        $response = $this->mediaCommentService->createReply($mediaComment, $text);

        return $this->response(new MediaCommentResource($response), '', 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MediaComment $mediaComment): Response
    {
        $this->authorize('delete', [MediaComment::class, $mediaComment]);

        if (auth()->check()) {
            $this->mediaCommentService->setUser(auth()->user());
        }

        $response = $this->mediaCommentService->delete($mediaComment);

        return $this->response($response, '', Response::HTTP_NO_CONTENT);
    }
}