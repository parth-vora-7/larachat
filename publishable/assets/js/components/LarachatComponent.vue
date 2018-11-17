<template>
    <div>
        <div id="chat-sidebar">
            <div v-for="user in users" class="sidebar-user-box" @click="openChatRoom(user.id)">
                <img src="vendor/larachat/larachat-user.png" />
                <span class="chat-username">{{ user.name }}</span>
            </div>
        </div>
        <div class="msg_box" v-for="chatRoom in activeChatRooms" :style="{ right: chatRoom.popupPosition + 'px' }">
            <div class="msg_head">{{ chatRoom.receiver_name }}
                <div class="close-chat" @click="closeChatRoom(chatRoom.id)">x</div>
            </div>
            <div class="msg_wrap">
                <div class="msg_body" :ref="'msg_body_' + chatRoom.id">
                    <div v-for="message in chatRoom.messages" :style="[(message.sender_id == loggedInUser.id) ? {'float': 'right'} : {'float': 'left'}]" :class="[(message.sender_id == loggedInUser.id) ? 'message' : 'reply']">{{ message.message }}</div>
                </div>
                <div class="msg_footer">
                    <textarea class="msg_input" rows="4" :ref="'msg_input_' + chatRoom.id" @keydown.enter.exact.prevent @keyup.enter.exact="sendMessage(chatRoom.id, $event)"></textarea>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            users: [],
            popupGap: 260,
            activeChatRooms: [],
            loggedInUser: []
        }
    },
    mounted() {
        var self = this;
        window.axios.get('larachat/users')
        .then(function (response) {
            self.users = response.data.data.users;
            self.loggedInUser = response.data.data.loggedInUser;

            window.Echo.private(`new-message-for-` + self.loggedInUser.id)
            .listen('.larachat.new.chat.message', (e) => {
                self.appendNewMessage(e.chatMessage);
            });
        })
        .catch(function (error) {
            console.log(error);
        });
    },
    methods: {
        openChatRoom(userId) {
            let self = this;
            let chatRoomId = self.isChatRoomOpen(userId);
            if(!chatRoomId) {
                self.getChatRoom(userId).then(chatRoom => {
                    chatRoom['popupPosition'] = self.getPopupPosition();
                    self.activeChatRooms.push(chatRoom);
                    self.updateChat(chatRoom);
                });
            }
        },
        appendNewMessage(message) {
            let self = this;
            let chatRoomId = self.isChatRoomOpen(message.sender_id);
            if(!chatRoomId) {
                self.getChatRoom(message.sender_id).then(chatRoom => {
                    chatRoom['popupPosition'] = self.getPopupPosition();
                    self.activeChatRooms.push(chatRoom);
                    self.updateChat(chatRoom);
                });
            } else {
                self.activeChatRooms.forEach((el) => {
                    if(el.id == message.chat_room_id) {
                        el.messages.push(message);
                    }
                    self.focusChatBox(message.chat_room_id);
                });
            }
        },
        updateChat(chatRoom) {
            let self = this;
            self.activeChatRooms.forEach((el) => {
                if(el.id == chatRoom.id) {
                    el.messages = chatRoom.messages;
                }
                self.focusChatBox(chatRoom.id);
            });
        },
        getChatRoom(userId) {
            let self = this;
            return window.axios.get('larachat/get-chat-room', {
                params: {
                    sender_id: self.loggedInUser.id, receiver_id: userId
                }
            })
            .then(response => response.data.data);
        },
        getPopupPosition() {
            let self = this;
            let popupPosition = 0;
            if(self.activeChatRooms[self.activeChatRooms.length-1]) {
                popupPosition = self.activeChatRooms[self.activeChatRooms.length-1].popupPosition;
            }
            return popupPosition + self.popupGap;
        },
        isChatRoomOpen(userId) {
            let self = this;
            let isChatRoomOpen = false;
            self.activeChatRooms.forEach((el) => {
                if(el.receiver_id == userId) {
                    isChatRoomOpen = el.id;
                }
            });
            return isChatRoomOpen;
        },
        focusChatBox(chatRoomId) {
            let self = this;
            setTimeout(function() {
                self.$refs["msg_body_" + chatRoomId][0].scrollTop = self.$refs["msg_body_" + chatRoomId][0].scrollHeight;
                self.$refs["msg_input_" + chatRoomId][0].focus();
            }, 200);
        },
        fetchChatRoomMessages(chatRoomId) {
            let self = this;
            return window.axios.get('larachat/' + chatRoomId + '/messages')
            .then(response => response.data.data);
        },
        closeChatRoom(chatRoomId) {
            this.activeChatRooms = this.activeChatRooms.filter(function(room) { return room.id != chatRoomId; });
        },
        sendMessage(chatRoomId, $event) {
            var self = this;
            var message = $event.target.value;
            if(message) {
                self.activeChatRooms.forEach((el, index) => {
                    if(el.id == chatRoomId) {
                        el.messages = el.messages || [];
                        window.axios.post('larachat/send-message', { chat_room_id: chatRoomId, message: message })
                        .then(function (response) {
                            el.messages.push(response.data.data);
                            self.focusChatBox(chatRoomId)
                            $event.target.value = '';
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                    }
                });
            }
        }
    },
}
</script>

<style>
#chat-sidebar {
    width: 250px;
    position: fixed;
    height: 100%;
    right: 0px;
    top: 0px;
    border: 1px solid rgba(0, 0, 0, 0.125);
    background-color: #f7f7f7;
    padding: 15px 0 15px 0px;
    margin-top: 55px;
    overflow: auto;
    z-index: 999;
}
.sidebar-user-box {
    padding: 4px 4px 4px 15px;
    margin-bottom: 4px;
    font-size: 15px;
    font-family: Calibri;
    font-weight:bold;
    cursor:pointer;
}
.sidebar-user-box:hover {
    background-color:#999999;
}
.sidebar-user-box:after {
    content: ".";
    display: block;
    height: 0;
    clear: both;
    visibility: hidden;
}
.chat-username {
    float:left;
    line-height:30px;
    margin-left:5px;
}
.sidebar-user-box img {
    width:35px;
    height:35px;
    border-radius:50%;
    float:left;
}
.msg_box {
    position:fixed;
    bottom:-5px;
    width:250px;
    background:white;
    border-radius:5px 5px 0px 0px;
}
.msg_head { 
    background:black;
    color:white;
    padding:8px;
    font-weight:bold;
    cursor:pointer;
    border-radius:5px 5px 0px 0px;
}
.msg_body {
    background:rgba(0, 0, 0, 0.03);
    height:200px;
    font-size:12px;
    padding:15px;
    overflow:auto;
    overflow-x: hidden;
    border: 1px solid #b2b2b2;
}
.msg_input {
    width:100%;
    height: 55px;
    border: 1px solid white;
    border-top:1px solid #DDDDDD;
    -webkit-box-sizing: border-box; 
    -moz-box-sizing: border-box;   
    box-sizing: border-box;  
    border: 1px solid #b2b2b2;
}
.close-chat {
    float:right;
    cursor:pointer;
}
.minimize {
    float:right;
    cursor:pointer;
    padding-right:5px; 
}
.msg-left {
    position:relative;
    background:#e2e2e2;
    padding:5px;
    min-height:10px;
    margin-bottom:5px;
    margin-right:10px;
    border-radius:5px;
    word-break: break-all;
}
.msg-right {
    background:#d4e7fa;
    padding:5px;
    min-height:15px;
    margin-bottom:5px;
    position:relative;
    margin-left:10px;
    border-radius:5px;
    word-break: break-all;
}
.message {
    float: right;
    background: #cdd8e0;
    font-size: 13px;
    font-weight: 600;
    border-radius: 8px 8px 0px 8px;
    padding: 3px 3px;
    margin: 8px 0;
    clear: both;
}
.reply {
    float: left;
    background: #cdd8e0;
    font-size: 13px;
    font-weight: 600;
    border-radius: 0px 8px 8px 8px;
    padding: 3px 3px;
    margin: 8px 0;
    clear: both;
}
</style>