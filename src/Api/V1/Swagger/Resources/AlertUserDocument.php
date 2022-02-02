<?php

/**
 * @OA\Schema()
 */
class AlertUserDocument
{
    /**
     * @OA\Property(property="_id", type="string", example="60237caf5e41025e1e3c80b1")
     * @OA\Property(property="type", type="integer", description="50 - user document rejected")
     * @OA\Property(property="status", type="integer", description="not_visited=0, visited=1", example="1")
     * @OA\Property(property="created_at", type="string", example="1612850111566")
     * @OA\Property(property="reason", type="string", example="This document is too blurry")
     */
}
