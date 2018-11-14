<?php

namespace xparthxvorax\larachat\Models;

use Illuminate\Database\Eloquent\Model;
use xparthxvorax\larachat\Models\ChatMessage;

class ChatRoom extends Model
{
	/**
     * Set the members for a chat room
     *
     * @param  array  $value
     * @return void
     */
	public function setRoomMembersAttribute(array $value)
	{
		sort($value);
		$this->attributes['room_members'] = implode(',', $value);
	}

    /**
     * Get the members of a chat room
     *
     * @param  string  $value
     * @return array
     */
    public function getRoomMembersAttribute($value)
    {
    	$roomMembersIds = explode(',', $value);
    	return User::whereIn('id', $roomMembersIds)->get(['id', 'name']);
    }

	/**
	 * Returns an existing chat room or create new one
	 *
	 * @param int senderId
	 * @param int $receiverId
	 * @return ChatRoom
	 */
	protected static function getChatRoom(int $senderId, int $receiverId)
	{
		$roomMembers = [$senderId, $receiverId];
		sort($roomMembers);
		$chatRoom = self::where('room_members', implode(',', $roomMembers))->first();
		if(empty($chatRoom)) {
			$chatRoom = new self();
			$chatRoom->creator_id = $senderId;
			$chatRoom->room_members = $roomMembers;
			$chatRoom->save();	
		}
		return $chatRoom->id;
	}

	/**
	 * Get room mnessages
	 *
	 * @return
	 */
	public function messages()
	{
		return $this->hasMany(ChatMessage::class);//->select(['id', 'sender_id', 'receiver_id', 'message']);
	}
}
