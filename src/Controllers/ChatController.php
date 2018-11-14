<?php

namespace xparthxvorax\larachat\Controllers;

use Exception;
use Illuminate\Http\Request;
use xparthxvorax\larachat\Models\ChatRoom;
use xparthxvorax\larachat\Models\ChatMessage;
use xparthxvorax\larachat\Models\User;
use xparthxvorax\larachat\Events\NewChatMessageEvent;
use xparthxvorax\larachat\Notifications\NewChatMessageNotification;

class ChatController
{
	/**
	 * Get all the users
	 *
	 * @return JSON
	 */
	public function getUsers()
	{
		try {
			$loggedInUser = auth()->user();
			$users = User::where('id', '!=', $loggedInUser->id)->get(['id', 'name']);
			return ['status' => true, 'data' => ['loggedInUser' => $loggedInUser, 'users' => $users]];
		} catch(Exception $ex) {
			return ['status' => false, 'error' => $ex->getMessage()];
		}
	}

	/**
	 * Get an existing chat room or create a new one
	 *
	 * @return JSON
	 */
	public function getChatRoom(Request $request)
	{
		try {
			$loggedInUser = auth()->user();
			$request->validate([
				'sender_id' => 'required',
				'receiver_id' => 'required',
			]);
			$chatRoomId = ChatRoom::getChatRoom($request->sender_id, $request->receiver_id);
			$chatRoom = ChatRoom::with('messages')->find($chatRoomId);
			foreach ($chatRoom['room_members'] as $key => $value) {
				if($value['id'] == $loggedInUser->id) {
					$chatRoom['sender_id'] = $value['id'];
					$chatRoom['sender_name'] = $value['name'];
				} else {
					$chatRoom['receiver_id'] = $value['id'];
					$chatRoom['receiver_name'] = $value['name'];
				}
			}
			return ['status' => true, 'data' => $chatRoom];
		} catch(Exception $ex) {
			return ['status' => false, 'error' => $ex->getMessage()];
		}
	}

	/**
	 * Get all messages of a chat room
	 *
	 * @param Request $request
	 * @return JSON
	 */
	public function getChatRoomMessages(Request $request, ChatRoom $chatRoom)
	{
		try {
			$loggedInUser = auth()->user();
			if(in_array($loggedInUser->id, $chatRoom->room_members->keyBy('id')->keys()->toArray()))
			{
				return ['status' => true, 'data' => $chatRoom->messages];
			}
			throw new Exception("Access Denied", 403);
		} catch(Exception $ex) {
			return ['status' => false, 'error' => $ex->getMessage()];
		}
	}

	/**
	 * Send a message
	 *
	 * @param Request $request
	 * @return JSON
	 */
	public function sendMessage(Request $request)
	{
		try {
			$request->validate([
				'chat_room_id' => 'required',
				'message' => 'required|string',
			]);
			$chatRoom = ChatRoom::findOrFail($request->chat_room_id);
			$senderId = auth()->user()->id;
			$receiverId = array_diff(array_column($chatRoom->room_members->toArray(), 'id'), [$senderId]);
			$receiverId = reset($receiverId);
			$receiver = User::find($receiverId);
			$message = $request->message;
			$chatMessageId = ChatMessage::saveMessage($chatRoom, $senderId, $receiverId, $message);
			$chatMessage = ChatMessage::with(['sender', 'receiver'])->find($chatMessageId);
			broadcast(new NewChatMessageEvent($chatMessage))->toOthers();
			//$receiver->notify(new NewChatMessageNotification($chatMessage));
			return ['status' => true, 'data' => $chatRoom->messages->pop()];
		} catch(Exception $ex) {
			return ['status' => false, 'error' => $ex->getMessage()];
		}
	}
}
