<?php

namespace Aparlay\Core\Api\V1\Controllers;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Api\V1\Resources\BlockResource;
use Aparlay\Core\Api\V1\Services\BlockService;
use Illuminate\Http\Response;

class BlockController extends Controller
{
    protected $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     */
    public function store(User $user): Response
    {
        $response = $this->blockService->create($user);

        return $this->response(new BlockResource($response['data']), '', $response['statusCode']);
    }

    /**
     */
    public function destroy(User $user): Response
    {
        // Unblock the user or throw exception if not Blocked
        $response = $this->blockService->unBlock($user);

        return $this->response($response, '', Response::HTTP_NO_CONTENT);
    }
}
