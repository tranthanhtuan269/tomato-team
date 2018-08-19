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

class AddComment implements ShouldBroadcast
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
            'message' => $this->conversation->message,
            'comment' => $this->conversation->comment,
            'type' => $this->conversation->type,
            'status' => $this->conversation->status,
            'user' => [
                'id' => $this->conversation->user->id,
                'name' => $this->conversation->user->name,
            ]
        ];
    }
}
