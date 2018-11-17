<?php

use xparthxvorax\larachat\Models\ChatRoom;

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'larachat', 'namespace' => 'xparthxvorax\larachat\Controllers'], function () {
	Route::get('/users', 'ChatController@getUsers');
	Route::get('/get-chat-room', 'ChatController@getChatRoom');
	Route::get('/{chatRoom}/messages', 'ChatController@getChatRoomMessages');
	Route::post('/send-message', 'ChatController@sendMessage');
});

Broadcast::routes();

Broadcast::channel('new-message-for-{userId}', function ($user, $userId) {
	return $user->id == $userId;
});