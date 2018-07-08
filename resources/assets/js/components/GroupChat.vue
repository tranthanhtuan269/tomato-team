<template>
    <div>
    <div class="panel panel-primary">
            <div class="panel-heading" id="accordion">
                <span class="glyphicon glyphicon-comment"></span> {{ group.name }}
            </div>
            <div class="panel-body chat-panel">
                <div class="panel panel-primary">
                    <div class="panel-heading" id="accordion">
                        <span class="glyphicon glyphicon-comment"></span> Conversation
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
                                    <p>
                                        {{ conversation1.message }}
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="panel panel-primary">
                    <div class="panel-heading" id="accordion">
                        <span class="glyphicon glyphicon-comment"></span> Group 1
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
                                    <p>
                                        {{ conversation.message }}
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="input-group">
                    <input id="btn-input" type="text" class="form-control input-sm" placeholder="Type your message here..." v-model="message" @keyup.enter="store()" autofocus />
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
        props: ['group'],

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
                axios.post('/conversations', {message: this.message, group_id: this.group.id})
                .then((response) => {
                    this.message = '';
                    //this.conversations.push(response.data);
                });
            },

            listenForNewMessage() {
                Echo.private('groups.' + this.group.id)
                    .listen('NewMessage', (e) => {
                        console.log(e);
                        if(e.user.name == 'admin'){
                            this.conversations1.push(e);
                        }else{
                            this.conversations.push(e);
                        }
                    });
            }
        }
    }
</script>
