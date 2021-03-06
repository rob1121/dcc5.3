<?php namespace App\Events\External;

use App\CustomerSpec;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;

class Review
{
    use InteractsWithSockets, SerializesModels;
    public $spec;

    /**
     * Create a new event instance.
     *
     * @param $spec
     */
    public function __construct(CustomerSpec $spec)
    {
        $this->spec = $spec;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
