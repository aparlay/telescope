<?php

namespace Aparlay\Core\Api\V1\Resources;

use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Helpers\Cdn;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    use SimpleUserTrait;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     *
     * @throws \Exception
     */
    public function toArray($request)
    {
        $people = [];
        foreach ($this->people as $person) {
            $people[] = $this->createSimpleUser($person);
        }

        $likes = [];
        foreach ($this->likes as $like) {
            $likes[] = $this->createSimpleUser($like);
        }

        $visits = [];
        foreach ($this->visits as $visit) {
            $visits[] = $this->createSimpleUser($visit);
        }

        return [
            '_id' => (string) $this->_id,
            'description' => $this->description,
            'hash' => $this->hash,
            'size' => $this->size,
            'length' => $this->length,
            'mime_type' => $this->mime_type,
            'visibility' => Media::getVisibilities()[$this->visibility],
            'status' => Media::getStatuses()[$this->status],
            'hashtags' => $this->hashtags,
            'people' => $people,
            'file' => $this->file, // TODO use Cdn
            'cover' => Cdn::cover($this->is_completed ? $this->filename.'.jpg' : 'default.jpg'),
            'creator' => $this->createSimpleUser($this->creator),
            'is_liked' => (bool) $this->is_liked,
            'is_visited' => (bool) $this->is_visited,
            'is_adult' => (bool) $this->is_adult,
            'like_count' => (int) $this->like_count,
            'likes' => $likes,
            'visit_count' => (int) $this->visit_count,
            'visits' => $visits,
            'comment_count' => (int) $this->comment_count,
            'comments' => [],
            'slug' => $this->slug,
            'alerts' => AlertResource::collection($this->alerts),
            'created_by' => (string) $this->created_by,
            'updated_by' => (string) $this->updated_by,
            'created_at' => $this->created_at->valueOf(),
            'updated_at' => $this->updated_at->valueOf(),
            '_links' => [
                'self' => ['href' => route('core.api.v1.media.show', ['media' => $this])],
                'index' => ['href' => route('core.api.v1.user.media_list', ['user' => $this->userObj])],
            ],
        ];
    }
}
