<?php

namespace xparthxvorax\larachat\Models;

use Illuminate\Database\Eloquent\Model;
use xparthxvorax\larachat\Models\ChatRoom;

class ChatMessage extends Model
{
    /**
	 * Save a message
	 *
	 * @param ChatRoom chatRoom
	 * @param int senderId
	 * @param int $receiverId
	 * @param string $message
	 * @return ChatMessage
	 */
    protected static function saveMessage(ChatRoom $chatRoom, $senderId, $receiverId, $message)
    {
    	$chatMessage = new self();
    	$chatMessage->chat_room_id = $chatRoom->id;
    	$chatMessage->sender_id = $senderId;
    	$chatMessage->receiver_id = $receiverId;
    	$chatMessage->message = $message;
    	$chatMessage->save();
        return $chatMessage->id;
    }

    /**
     * Get message sender
     *
     * @return
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id')->select(['id', 'name']);
    }

    /**
     * Get message receiver
     *
     * @return
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id')->select(['id', 'name']);
    }
}
