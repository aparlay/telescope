<?php

namespace Aparlay\Core\Listeners;

use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Services\MediaCommentService;
use Aparlay\Payment\Events\TipCreatedEvent;

class CreateTipMediaComment
{
    public function handle(TipCreatedEvent $event)
    {
        /** @var MediaCommentService $mediaCommentService */
        $mediaCommentService = app()->make(MediaCommentService::class);

        $tip = $event->getTip();

        if (empty($tip->media_id)) {
            return; // tips can send in chat (to user not to a media as well)
        }

        $text = __('sent a tip 🎉 :amount', [
            'amount' =>  (new Money($tip->amount, new Currency('USD')))->format(),
        ]);

        /** @var Media $media */
        $media = Media::query()->findOrFail($tip->mediaObj->_id);
        $mediaCommentService->setUser($tip->creatorObj);
        $mediaCommentService->create($media, $text);
    }
}
