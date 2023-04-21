<?php

namespace Aparlay\Core\Events;

use Aparlay\Core\Helpers\Cdn;
use Aparlay\Core\Models\Media;
use Exception;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaProcessingCompletedEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * The credit card instance.
     *
     * @var Media
     */
    public $media;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('users.' . $this->media->creator['_id']);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'Media.StatusUpdated';
    }

    /**
     * Get the data to broadcast.
     *
     * @throws Exception
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'media' => [
                '_id' => (string) $this->media->_id,
                'file' => Cdn::video($this->media->is_completed ? $this->media->file : 'default.mp4'),
                'cover' => Cdn::cover($this->media->is_completed ? $this->media->filename . '.jpg' : 'default.jpg'),
                'status' => $this->media->status,
            ],
            'message' => 'All done',
            'progress' => 100,
        ];
    }
}
