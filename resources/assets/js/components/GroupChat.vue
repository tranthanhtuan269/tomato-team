<template>
    <div>
        <div class="row btn-status">
            <div class="col-sm-12">
                <button v-if="done == 0" class="btn btn-danger pull-right btn-control" v-on:click="changeStatus()">Done</button>
                <button v-if="done == 1" class="btn btn-success pull-right btn-control">Done</button>
                <button v-if="done == 2" class="btn btn-warning pull-right btn-control" v-on:click="changeStatus3()">Done</button>
                <button v-if="done == 3" class="btn btn-success pull-right btn-control">Done</button>
                <a class="btn btn-primary pull-right btn-control" v-bind:href="'/exportWord?group='+ group.id +'&lang=eng'">Export ENG</a>
                <a class="btn btn-primary pull-right btn-control" v-bind:href="'/exportWord?group='+ group.id +'&lang=vie'">Export VIE</a>
                <button type="button" class="btn btn-primary pull-right btn-control" data-toggle="modal" data-target="#myModal">Import data</button>
                <button type="button" class="btn btn-primary pull-right btn-control" v-on:click="toggestChat(true)">Show Chat</button>
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
                        <div class="panel-body chat-panel" v-on:click="onChange(cv)" v-for="(cv, index) in conversations">
                            <div v-html="index + 1" class="conversation-index"></div>
                            <div v-html="cv.message" v-if="(iuser.type == 0 && !cv.showEditor) || iuser.type != 0"></div>
                            <quill-editor v-model="cv.message" v-on:change="change(cv.message, cv.conversation, 0)" v-if="iuser.type == 0 && cv.showEditor"
                                      ref="quillEditorA"
                                      :options="editorOption"/>
                        </div>
                    </div>
                    <div class="panel panel-primary col-sm-4 conversation-panel">
                        <div class="panel-heading text-center customize-panel2">
                            <b>VIE</b>
                        </div>
                        <div class="panel-body chat-panel" v-on:click="onChange(cv)" v-for="(cv, index) in conversations1">
                            <div v-html="index + 1" class="conversation-index"></div>
                            <div v-html="cv.message" v-if="((iuser.type == 0 || iuser.type == 1) && !cv.showEditor) || iuser.type == 2"></div>
                            <quill-editor v-model="cv.message" v-on:change="change(cv.message, cv.conversation, 1)" v-if="(iuser.type == 0 || iuser.type == 1) && cv.showEditor" ref="quillEditorB" :options="editorOption"/>
                        </div>
                    </div>
                    <div class="panel panel-primary col-sm-4 conversation-panel">
                        <div class="panel-heading text-center customize-panel3">
                            <b>ENG</b>
                        </div>
                        <div class="panel-body chat-panel" v-on:click="onChange(cv)" v-for="(cv, index) in conversations2">
                            <div v-html="index + 1" class="conversation-index"></div>
                            <div v-html="cv.message" v-if="((iuser.type == 0 || iuser.type == 2) && !cv.showEditor) || iuser.type == 1"></div>
                            <quill-editor v-model="cv.message" v-on:change="change(cv.message, cv.conversation, 2)" v-if="(iuser.type == 0 || iuser.type == 2) && cv.showEditor" ref="quillEditorC" :options="editorOption"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-primary general-panel" v-if="showChat==true">
            <div class="panel-heading text-center">
                <b>General</b> <i class="fas fa-minus pull-right" v-on:click="toggestChat(false)"></i>
            </div>
            <div class="panel-body">
                <ul id="general-content" class="list-group" v-chat-scroll>
                    <message v-for="m in listMessage" :key="m.id" :user="m.user.name">
                        {{ m.message }}
                    </message>
                </ul>
                <div class="form-inline"><input type="text" v-model="message" class="form-control control-long" @keyup.enter="SendMessage()"/><button class="btn btn-default control-small pull-right" v-on:click="SendMessage()">Send</button></div>
            </div>
        </div>
        <vue-import :group="group" :key="group.id"></vue-import>
    </div>
</template>

<script>
    export default {
        props: ['group', 'iuser'],
        computed: {
          editorA() {
            return this.$refs.quillEditorA.quill
          },
          editorB() {
            return this.$refs.quillEditorB.quill
          },
          editorC() {
            return this.$refs.quillEditorC.quill
          }
        },

        data() {
            return {
                conversations: [],
                conversations1: [],
                conversations2: [],
                message: '',
                listMessage: [],
                done: 0,
                group_id: this.group.id,
                showChat: false,
                editorOption: {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],        // toggled buttons
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

                            [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                            [{ 'align': [] }]
                        ]
                    },
                }
            }
        },

        mounted() {
            this.setCurrentStatus(this.group.status, this.group.status_admin, this.group.status_source, this.group.status_target)
            this.listenForNewMessage();
            this.listenForNewConversation();
        },

        created() {
            var sefl = this;
            this.listMessage = [];
            this.conversations = [];
            this.conversations1 = [];
            this.conversations2 = [];
            axios.get('/conversation/' + this.group.id + '/getListChat')
            .then(response => {
                var i;
                for(i = 0; i < response.data.length; i++){
                    this.listMessage.push(response.data[i]);
                }
            })
            .catch(e => {
                this.errors.push(e)
            });

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
            });
        },

        methods: {
            SendMessage(){
                if(this.message.length == 0) return;
                axios.post('/conversations', {message: this.message, group_id: this.group.id, type: -1, conversation: -1})
                .then((response) => {
                    this.listMessage.push(response.data);
                    this.message = "";
                });
            },
            toggestChat(status) {
                this.showChat = status;
            },
            onEditorBlur(quill) {
                console.log('editor blur!', quill)
            },
            onEditorFocus(quill) {
                console.log('editor focus!', quill)
            },
            onEditorReady(quill) {
                console.log('editor ready!', quill)
            },
            store(message, conversation, type) {
                axios.post('/conversations', {message: message, group_id: this.group.id, type: type, conversation: conversation})
                .then((response) => {
                    if(this.done == 1 || this.done == 3){
                        this.changeStatus2();
                    }
                });
            },

            listenForNewMessage() {
                Echo.private('groups.' + this.group.id)
                    .listen('NewMessage', (e) => {
                        if(e.type == -1){
                            this.listMessage.push(e);
                        }else if(e.type == 0){
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

            change(message, conversation, type){
                this.store(message, conversation, type);
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
                        this.conversations1[i].showEditor = false;
                        this.conversations2[i].showEditor = false;
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
