<?php

namespace Aparlay\Core\Api\V1\Resources;

use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use Illuminate\Http\Request;

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

        $tips = 0;
        $alerts = [];
        if (isset(auth()->user()->_id) && (string) auth()->user()->_id === (string) $this->creator['_id']) {
            $tips = new Money((int) $this->tips, new Currency('USD'));
            $alerts = $this->alerts;
        }

        return [
            '_id' => (string) $this->_id,
            'description' => $this->description,
            'hash' => $this->hash,
            'size' => $this->size,
            'length' => $this->length,
            'mime_type' => $this->mime_type,
            'visibility' => $this->visibility,
            'status' => $this->status,
            'hashtags' => $this->hashtags,
            'people' => $people,
            'file' => $this->file_url,
            'cover' => $this->cover_url,
            'creator' => $this->creator,
            'is_liked' => $this->is_liked,
            'is_visited' => $this->is_visited,
            'is_adult' => $this->is_adult,
            'like_count' => $this->like_count,
            'likes' => $likes,
            'visit_count' => $this->visit_count,
            'visits' => $visits,
            'comment_count' => $this->comment_count,
            'tips' => $tips,
            'tips_formatted' => new Money((int) $tips, new Currency('USD')),
            'sent_tips' => $this->sent_tips,
            'sent_tips_formatted' => new Money((int) $this->sent_tips, new Currency('USD')),
            'comments' => [],
            'slug' => $this->slug,
            'alerts' => AlertResource::collection($alerts),
            'created_by' => (string) $this->created_by,
            'updated_by' => (string) $this->updated_by,
            'created_at' => $this->created_at->valueOf(),
            'updated_at' => $this->updated_at->valueOf(),
            '_links' => [
                'self' => ['href' => route('core.api.v1.media.show', ['media' => $this])],
                'index' => ['href' => route('core.api.v1.user.media.list', ['user' => $this->creator['_id']])],
            ],
        ];
    }
}
