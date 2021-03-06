<?php

namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class update_user implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
     public $user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user=$user;
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel("update_user");//Private
    }

    public function broadcastWith(){
        return [
            'id' =>$this->user['id'],
            'name' =>$this->user['name'],
            'email' =>$this->user['email'],
            'password' =>$this->user['password'],
            'type' =>$this->user['type'],
            'bio' =>$this->user['bio']
        ];
    }
}
