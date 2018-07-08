<template>
    <div>
    <div class="panel panel-primary">
            <div class="panel-heading">
                {{ group.name }}
            </div>
            <div class="panel-body chat-panel">
                <div class="panel panel-primary col-sm-4">
                    <div class="panel-heading text-center">
                        Conversation
                    </div>
                    <div class="panel-body chat-panel">
                        <ul class="chat">
                            <li v-for="conversation in conversations">
                            <!-- <span class="chat-img pull-left">
                                <img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-circle" />
                            </span> -->
                                <div class="chat-body clearfix">
                                    <div class="header">
                                        <strong class="primary-font">{{ conversation.user.name }}</strong>
                                    </div>
                                    <p v-html="conversation.message">
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="panel panel-primary col-sm-4">
                    <div class="panel-heading text-center">
                        Source Team
                    </div>
                    <div class="panel-body chat-panel">
                        <ul class="chat">
                            <li v-for="conversation1 in conversations1">
                            <!-- <span class="chat-img pull-left">
                                <img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-circle" />
                            </span> -->
                                <div class="chat-body clearfix">
                                    <div class="header">
                                        <strong class="primary-font">{{ conversation1.user.name }}</strong>
                                    </div>
                                    <p v-html="conversation1.message">
                                        
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="panel panel-primary col-sm-4">
                    <div class="panel-heading text-center">
                        Target Team
                    </div>
                    <div class="panel-body chat-panel">
                        <ul class="chat">
                            <li v-for="conversation2 in conversations2">
                            <!-- <span class="chat-img pull-left">
                                <img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-circle" />
                            </span> -->
                                <div class="chat-body clearfix">
                                    <div class="header">
                                        <strong class="primary-font">{{ conversation2.user.name }}</strong>
                                    </div>
                                    <p v-html="conversation2.message">
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="input-group">
                    <wysiwyg v-model="message" v-on:change="change"/>
                    <span class="input-group-btn">
                        <button class="btn btn-warning btn-sm" id="btn-chat" @click.prevent="store()">
                            Send</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['group', 'iuser'],

        data() {
            return {
                conversations: [],
                conversations1: [],
                conversations2: [],
                message: '',
                group_id: this.group.id
            }
        },

        mounted() {
            this.listenForNewMessage();
        },

        methods: {
            store() {
                console.log(this.iuser);
                axios.post('/conversations', {message: this.message, group_id: this.group.id, type: this.iuser.type})
                .then((response) => {
                    if(this.iuser.type == 0){
                        this.conversations = [];
                        this.conversations.push(response.data);
                    }else if(this.iuser.type == 1){
                        this.conversations1 = [];
                        this.conversations1.push(response.data);
                    }else if(this.iuser.type == 2){
                        this.conversations2 = [];
                        this.conversations2.push(response.data);
                    }
                });
            },

            listenForNewMessage() {
                Echo.private('groups.' + this.group.id)
                    .listen('NewMessage', (e) => {
                        if(e.type == 0){
                            this.conversations = [];
                            this.conversations.push(e);
                        }else if(e.type == 1){
                            this.conversations1 = [];
                            this.conversations1.push(e);
                        }else if(e.type == 2){
                            this.conversations2 = [];
                            this.conversations2.push(e);
                        }
                    });
            },

            change(){
                this.store();
            }
        }
    }
</script>
