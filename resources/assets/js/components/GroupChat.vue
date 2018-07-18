<template>
    <div>
    <div class="row btn-status">
        <div class="col-sm-12">
            <button v-if="done == 0" class="btn btn-danger pull-right btn-control" v-on:click="changeStatus()">Done</button>
            <button v-if="done == 1" class="btn btn-success pull-right btn-control">Done</button>
            <button v-if="done == 2" class="btn btn-warning pull-right btn-control" v-on:click="changeStatus3()">Done</button>
            <button v-if="done == 3" class="btn btn-success pull-right btn-control">Done</button>
            <a class="btn btn-success pull-right btn-control" v-bind:href="'/exportWord?group='+ group.id +'&lang=eng'">Export ENG</a>
            <a class="btn btn-success pull-right btn-control" v-bind:href="'/exportWord?group='+ group.id +'&lang=vie'">Export VIE</a>
        </div>
    </div>
    <div class="panel panel-primary">
            <div class="panel-heading">
                {{ group.name }}
            </div>
            <div class="panel-body chat-panel">
                <div class="panel panel-primary col-sm-4 conversation-panel">
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
                                    <!-- <div class="header">
                                        <strong class="primary-font">{{ conversation.user.name }}</strong>
                                    </div> -->
                                    <p v-html="conversation.message">
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="panel panel-primary col-sm-4 conversation-panel">
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
                                    <!-- <div class="header">
                                        <strong class="primary-font">{{ conversation1.user.name }}</strong>
                                    </div> -->
                                    <p v-html="conversation1.message">
                                        
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="panel panel-primary col-sm-4 conversation-panel">
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
                                    <!-- <div class="header">
                                        <strong class="primary-font">{{ conversation2.user.name }}</strong>
                                    </div> -->
                                    <p v-html="conversation2.message">
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <wysiwyg v-model="message" v-on:change="change"/>
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
                done: 0,
                group_id: this.group.id
            }
        },

        mounted() {
            this.setCurrentStatus(this.group.status, this.group.status_admin, this.group.status_source, this.group.status_target)
            this.listenForNewMessage();
        },

        created() {
        
            axios.get('/conversation/' + this.group.id + '/later/0')
            .then(response => {
                this.conversations = [];
                this.conversations.push(response.data);
                if(this.iuser.type == 0){
                    this.message = response.data.message;
                }
            })
            .catch(e => {
                this.errors.push(e)
            })

            axios.get('/conversation/' + this.group.id + '/later/1')
            .then(response => {
                this.conversations1 = [];
                this.conversations1.push(response.data);
                if(this.iuser.type == 1){
                    this.message = response.data.message;
                }
            })
            .catch(e => {
                this.errors.push(e)
            })

            axios.get('/conversation/' + this.group.id + '/later/2')
            .then(response => {
                this.conversations2 = [];
                this.conversations2.push(response.data);
                if(this.iuser.type == 2){
                    this.message = response.data.message;
                }
            })
            .catch(e => {
                this.errors.push(e)
            })
        },

        methods: {
            store() {
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
                    if(this.done == 1 || this.done == 3){
                        this.changeStatus2();
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
            },

            changeStatus(){
                var self = this;
                if(this.iuser.type == 0){
                    axios.post('/group/' + this.group.id + '/done', {group_id: this.group.id, type: this.iuser.type, statusType: "admin", status: 1})
                        .then((response) => {
                            self.done = 1;
                        });
                }else if(this.iuser.type == 1){
                    axios.post('/group/' + this.group.id + '/done', {group_id: this.group.id, type: this.iuser.type, statusType: "source", status: 1})
                        .then((response) => {
                            console.log(response);
                            self.done = 1;
                        });
                }else if(this.iuser.type == 2){
                    axios.post('/group/' + this.group.id + '/done', {group_id: this.group.id, type: this.iuser.type, statusType: "target", status: 1})
                        .then((response) => {
                            console.log(response);
                            self.done = 1;
                        });
                }
            },

            changeStatus2(){
                var self = this;
                if(this.iuser.type == 0){
                    axios.post('/group/' + this.group.id + '/done', {group_id: this.group.id, type: this.iuser.type, statusType: "admin", status: 2})
                        .then((response) => {
                            self.done = 2;
                        });
                }else if(this.iuser.type == 1){
                    axios.post('/group/' + this.group.id + '/done', {group_id: this.group.id, type: this.iuser.type, statusType: "source", status: 2})
                        .then((response) => {
                            console.log(response);
                            self.done = 2;
                        });
                }else if(this.iuser.type == 2){
                    axios.post('/group/' + this.group.id + '/done', {group_id: this.group.id, type: this.iuser.type, statusType: "target", status: 2})
                        .then((response) => {
                            console.log(response);
                            self.done = 2;
                        });
                }
            },

            changeStatus3(){
                var self = this;
                if(this.iuser.type == 0){
                    axios.post('/group/' + this.group.id + '/done', {group_id: this.group.id, type: this.iuser.type, statusType: "admin", status: 3})
                        .then((response) => {
                            self.done = 3;
                        });
                }else if(this.iuser.type == 1){
                    axios.post('/group/' + this.group.id + '/done', {group_id: this.group.id, type: this.iuser.type, statusType: "source", status: 3})
                        .then((response) => {
                            console.log(response);
                            self.done = 3;
                        });
                }else if(this.iuser.type == 2){
                    axios.post('/group/' + this.group.id + '/done', {group_id: this.group.id, type: this.iuser.type, statusType: "target", status: 3})
                        .then((response) => {
                            console.log(response);
                            self.done = 3;
                        });
                }
            },

            setCurrentStatus(status, status_admin, status_source, status_target){
                if(this.iuser.type == 0){
                    this.done = status_admin;
                }else if(this.iuser.type == 1){
                    this.done = status_source;
                }else if(this.iuser.type == 2){
                    this.done = status_target;
                }
            },

            exportWord(groupID, lang){
                alert(groupID);
            }
        }
    }
</script>
