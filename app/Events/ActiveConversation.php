<?php

namespace App\Events;

use App\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActiveConversation implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('groups.' . $this->conversation->group->id);
    }

    public function broadcastWith()
    {
        return [
            'conversation' => $this->conversation->conversation,
            'group' => $this->conversation->group->id,
            'type' => $this->conversation->type,
            'user' => [
                'id' => $this->conversation->updateBy->id,
                'name' => $this->conversation->updateBy->name,
            ]
        ];
    }
}
