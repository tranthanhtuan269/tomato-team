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
                <button type="button" class="btn btn-primary pull-right btn-control" data-toggle="modal" data-target="#myModal">Import data</button>
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                {{ group.name }}
                <span class="add-conversation" v-if="iuser.type == 0" v-on:click="addConversation()"><i class="fas fa-plus"></i> Add Conversation</span>
            </div>
            <div class="panel-body chat-panel">
                <div class="row">
                    <div class="panel panel-primary col-sm-4 conversation-panel">
                        <div class="panel-heading text-center customize-panel1">
                            <b>KOR</b>
                        </div>
                        <div class="panel-body chat-panel" v-on:click="onChange(cv)" v-for="cv in conversations">
                            <div v-html="cv.message" v-if="(iuser.type == 0 && !cv.showEditor) || iuser.type != 0"></div>
                            <wysiwyg v-model="cv.message" v-on:change="change(cv.message, cv.conversation)" v-if="iuser.type == 0 && cv.showEditor"/>
                        </div>
                    </div>
                    <div class="panel panel-primary col-sm-4 conversation-panel">
                        <div class="panel-heading text-center customize-panel2">
                            <b>VIE</b>
                        </div>
                        <div class="panel-body chat-panel" v-on:click="onChange(cv)" v-for="cv in conversations1">
                            <div v-html="cv.message" v-if="(iuser.type == 1 && !cv.showEditor) || iuser.type != 1"></div>
                            <wysiwyg v-model="cv.message" v-on:change="change(cv.message, cv.conversation)" v-if="iuser.type == 1 && cv.showEditor"/>
                        </div>
                    </div>
                    <div class="panel panel-primary col-sm-4 conversation-panel">
                        <div class="panel-heading text-center customize-panel3">
                            <b>ENG</b>
                        </div>
                        <div class="panel-body chat-panel" v-on:click="onChange(cv)" v-for="cv in conversations2">
                            <div v-html="cv.message" v-if="(iuser.type == 2 && !cv.showEditor) || iuser.type != 2"></div>
                            <wysiwyg v-model="cv.message" v-on:change="change(cv.message, cv.conversation)" v-if="iuser.type == 2 && cv.showEditor"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <vue-import :group="group" :key="group.id"></vue-import>
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
            this.listenForNewConversation();
        },

        created() {
            var sefl = this;
            this.conversations = [];
            this.conversations1 = [];
            this.conversations2 = [];
            axios.get('/conversation/' + this.group.id + '/getListConversation')
            .then(response => {
                var i;
                for(i = 0; i < response.data.length; i++){
                    response.data[i].showEditor = false;
                    if(response.data[i].type == 0){
                        this.conversations.push(response.data[i]);
                    }else if(response.data[i].type == 1){
                        this.conversations1.push(response.data[i]);
                    }else{
                        this.conversations2.push(response.data[i]);
                    }
                }
            })
            .catch(e => {
                this.errors.push(e)
            })
        },

        methods: {
            store(message, conversation) {
                axios.post('/conversations', {message: message, group_id: this.group.id, type: this.iuser.type, conversation: conversation})
                .then((response) => {
                    if(this.done == 1 || this.done == 3){
                        this.changeStatus2();
                    }
                });
            },

            listenForNewMessage() {
                Echo.private('groups.' + this.group.id)
                    .listen('NewMessage', (e) => {
                        if(e.type == 0){
                            var i = 0; 
                            for(i = 0; i < this.conversations.length; i++){
                                if(this.conversations[i].conversation == e.conversation){
                                    this.conversations[i].message = e.message;
                                }
                            }
                        }else if(e.type == 1){
                            var i = 0; 
                            for(i = 0; i < this.conversations.length; i++){
                                if(this.conversations1[i].conversation == e.conversation){
                                    this.conversations1[i].message = e.message;
                                }
                            }
                        }else if(e.type == 2){
                            var i = 0; 
                            for(i = 0; i < this.conversations.length; i++){
                                if(this.conversations2[i].conversation == e.conversation){
                                    this.conversations2[i].message = e.message;
                                }
                            }
                        }
                    });
            },

            listenForNewConversation() {
                Echo.private('groups.' + this.group.id)
                    .listen('AddConversation', (e) => {
                        this.$snotify.error('A conversation has been created! Refresh to update content!', {
                            timeout: 60000,
                            showProgressBar: true,
                            closeOnClick: true,
                            pauseOnHover: true
                        });
                    });
            },

            change(message, conversation){
                this.store(message, conversation);
            },

            onChange(cv){
                this.hideAllEditor();
                cv.showEditor = true;
            },

            hideAllEditor(){
                if(this.iuser.type == 0){
                    var i = 0; 
                    for(i = 0; i < this.conversations.length; i++){
                        this.conversations[i].showEditor = false;
                    }
                }else if(this.iuser.type == 1){
                    var i = 0; 
                    for(i = 0; i < this.conversations1.length; i++){
                        this.conversations1[i].showEditor = false;
                    }
                }else if(this.iuser.type == 2){
                    var i = 0; 
                    for(i = 0; i < this.conversations2.length; i++){
                        this.conversations2[i].showEditor = false;
                    }
                }
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

            addConversation(){
                if(this.iuser.type == 0){
                    axios.post('/group/' + this.group.id + '/addConversation', {group_id: this.group.id, type: this.iuser.type})
                            .then((response) => {
                                var i;
                                for(i = 0; i < response.data.length; i++){
                                    response.data[i].showEditor = false;
                                    if(response.data[i].type == 0){
                                        this.conversations.push(response.data[i]);
                                    }else if(response.data[i].type == 1){
                                        this.conversations1.push(response.data[i]);
                                    }else{
                                        this.conversations2.push(response.data[i]);
                                    }
                                }
                            });
                }
            }
        }
    }
</script>
