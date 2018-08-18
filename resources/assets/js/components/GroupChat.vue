<template>
    <div>
        <div class="row btn-status">
            <div class="col-sm-12">
                <button v-if="!done" class="btn btn-danger pull-right btn-control">Processing</button>
                <button v-if="done" class="btn btn-success pull-right btn-control">Done</button>
                <a class="btn btn-primary pull-right btn-control" v-bind:href="'/exportWord?group='+ group.id +'&lang=eng'">Export ENG</a>
                <a class="btn btn-primary pull-right btn-control" v-bind:href="'/exportWord?group='+ group.id +'&lang=vie'">Export VIE</a>
                <button type="button" class="btn btn-primary pull-right btn-control" data-toggle="modal" data-target="#myModal">Import data</button>
                <button type="button" class="btn btn-primary pull-right btn-control chat-btn" v-on:click="toggestChat(true)">Show Chat <span class="note-chat" v-if="note_message == true">!</span></button>
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
                        <div v-for="(cv, index) in conversations" v-bind:class="[defaultClass, 'conversation-'+index, cv.isActive? activeClass: '']">
                            <div class="group-status">
                                <div v-html="index + 1" class="conversation-index"></div>
                                <div v-if="cv.status==0" class="conversation-status" v-on:click="changeStatusConversation(cv, 0, cv.status)">P</div>
                                <div v-if="cv.status==1" class="conversation-status conversation-done" v-on:click="changeStatusConversation(cv, 0, cv.status)">D</div>
                            </div>
                            <span class="user-active" v-html="cv.userActive" v-if="cv.userActive.length > 0"></span>
                            <div v-html="cv.message" v-if="(iuser.type == 0 && !cv.showEditor) || iuser.type != 0" class="conversation-content" v-on:click="onChange(cv, 0)"></div>
                            <quill-editor v-model="cv.message" 
                            @keyup.space.native.capture.prevent="change(cv, 0)" 
                            @keyup.191.native.capture.prevent="change(cv, 0)" 
                            @keyup.190.native.capture.prevent="change(cv, 0)" 
                            @keyup.188.native.capture.prevent="change(cv, 0)" 
                            @keyup.49.native.capture.prevent="change(cv, 0)" 
                            @keyup.13.native.capture.prevent="change(cv, 0)" 
                            v-on:change="save(cv, 0)"
                            v-if="iuser.type == 0 && cv.showEditor"
                                      ref="quillEditorA"
                                      :options="editorOption"/>
                        </div>
                    </div>
                    <div class="panel panel-primary col-sm-4 conversation-panel">
                        <div class="panel-heading text-center customize-panel2">
                            <b>VIE</b>
                        </div>
                        <div v-for="(cv, index) in conversations1" v-bind:class="[defaultClass, 'conversation-'+index, cv.isActive? activeClass: '']">
                            <div class="group-status">
                                <div v-html="index + 1" class="conversation-index"></div>
                                <div v-if="cv.status==0" class="conversation-status" v-on:click="changeStatusConversation(cv, 1, cv.status)">P</div>
                                <div v-if="cv.status==1" class="conversation-status conversation-done" v-on:click="changeStatusConversation(cv, 1, cv.status)">D</div>
                            </div>
                            <span class="user-active" v-html="cv.userActive" v-if="cv.userActive.length > 0"></span>
                            <div v-html="cv.message" v-if="((iuser.type == 0 || iuser.type == 1) && !cv.showEditor) || iuser.type == 2" class="conversation-content" v-on:click="onChange(cv, 1)"></div>
                            <quill-editor v-model="cv.message" 
                            @keyup.space.native.capture.prevent="change(cv, 1)" 
                            @keyup.191.native.capture.prevent="change(cv, 1)" 
                            @keyup.190.native.capture.prevent="change(cv, 1)" 
                            @keyup.188.native.capture.prevent="change(cv, 1)" 
                            @keyup.49.native.capture.prevent="change(cv, 1)" 
                            @keyup.13.native.capture.prevent="change(cv, 1)" 
                            v-on:change="save(cv, 1)"
                            v-if="(iuser.type == 0 || iuser.type == 1) && cv.showEditor" ref="quillEditorB" :options="editorOption"/>
                        </div>
                    </div>
                    <div class="panel panel-primary col-sm-4 conversation-panel">
                        <div class="panel-heading text-center customize-panel3">
                            <b>ENG</b>
                        </div>
                        <div v-for="(cv, index) in conversations2" v-bind:class="[defaultClass, 'conversation-'+index, cv.isActive? activeClass: '']">
                            <div class="group-status">
                                <div v-html="index + 1" class="conversation-index"></div>
                                <div v-if="cv.status==0" class="conversation-status" v-on:click="changeStatusConversation(cv, 2, cv.status)">P</div>
                                <div v-if="cv.status==1" class="conversation-status conversation-done" v-on:click="changeStatusConversation(cv, 2, cv.status)">D</div>
                            </div>
                            <span class="user-active" v-html="cv.userActive" v-if="cv.userActive.length > 0"></span>
                            <div v-html="cv.message" v-if="((iuser.type == 0 || iuser.type == 2) && !cv.showEditor) || iuser.type == 1" class="conversation-content" v-on:click="onChange(cv, 2)"></div>
                            <quill-editor v-model="cv.message" 
                            @keyup.space.native.capture.prevent="change(cv, 2)" 
                            @keyup.191.native.capture.prevent="change(cv, 2)" 
                            @keyup.190.native.capture.prevent="change(cv, 2)" 
                            @keyup.188.native.capture.prevent="change(cv, 2)" 
                            @keyup.49.native.capture.prevent="change(cv, 2)" 
                            @keyup.13.native.capture.prevent="change(cv, 2)" 
                            v-on:change="save(cv, 2)"
                            v-if="(iuser.type == 0 || iuser.type == 2) && cv.showEditor" ref="quillEditorC" :options="editorOption"/>
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
                activeClass: 'alert alert-danger',
                defaultClass: 'panel-body chat-panel',
                conversations: [],
                conversations1: [],
                conversations2: [],
                message: '',
                note_message: false,
                // timeCost: 10,
                timeCost: 1000,
                min_height: 184,
                listMessage: [],
                done: false,
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
            this.done = this.group.status;
            this.listenNewMessage();
            this.listenUpdateMessage();
            this.listenAddConversation();
            this.listenActiveConversation();
            this.listenStatusConversation();
            this.listenAutoSaveConversation();
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
                    this.note_message = true;
                }
            })
            .catch(e => {
                this.errors.push(e)
            });

            axios.get('/conversation/' + this.group.id + '/getListConversation')
            .then(response => {
                var i;
                for(i = 0; i < response.data.length; i++){
                    response.data[i].timeCount = 0;
                    response.data[i].isActive = false;
                    response.data[i].showEditor = false;
                    response.data[i].userActive = '';
                    if(response.data[i].type == 0){
                        this.conversations.push(response.data[i]);
                    }else if(response.data[i].type == 1){
                        this.conversations1.push(response.data[i]);
                    }else{
                        this.conversations2.push(response.data[i]);
                    }
                }
                this.checkStatusGroup();
            })
            .catch(e => {
                this.errors.push(e)
            });
        },

        updated(){
            console.log("run updated");
            var i = 0;
            for(i = 0; i < this.conversations.length; i++){
                var maxHeight = this.min_height;
                $(".conversation-"+i).each(function(){
                    if ($(this).find('.quill-editor').height() > maxHeight) { 
                        maxHeight = $(this).find('.quill-editor').height(); 
                    }
                    if ($(this).find('.conversation-content>p').height() > maxHeight) { 
                        maxHeight = $(this).find('.conversation-content>p').height(); 
                    }
                    console.log(maxHeight);
                });
                $(".conversation-"+i).height(maxHeight);
            }

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
                this.note_message = false;
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
            store(cv, type) {
                axios.post('/conversations', {message: cv.message, group_id: this.group.id, type: type, conversation: cv.conversation})
                .then((response) => {
                    cv.status = 0;
                });
            },
            save(cv, type) {
                cv.timeCount = this.timeCost;
            },

            listenNewMessage() {
                Echo.private('groups.' + this.group.id)
                    .listen('NewMessage', (e) => {
                        if(e.type == -1){
                            this.listMessage.push(e);
                            this.note_message = true;
                        }else if(e.type == 0){
                            var i = 0; 
                            for(i = 0; i < this.conversations.length; i++){
                                if(this.conversations[i].conversation == e.conversation){
                                    this.conversations[i].message = e.message;
                                    this.conversations[i].status = 0;
                                    this.conversations[i].timeCount = this.timeCost;
                                    this.conversations[i].isActive = true;
                                }
                            }
                        }else if(e.type == 1){
                            var i = 0; 
                            for(i = 0; i < this.conversations.length; i++){
                                if(this.conversations1[i].conversation == e.conversation){
                                    this.conversations1[i].message = e.message;
                                    this.conversations1[i].status = 0;
                                    this.conversations1[i].timeCount = this.timeCost;
                                    this.conversations1[i].isActive = true;
                                }
                            }
                        }else if(e.type == 2){
                            var i = 0; 
                            for(i = 0; i < this.conversations.length; i++){
                                if(this.conversations2[i].conversation == e.conversation){
                                    this.conversations2[i].message = e.message;
                                    this.conversations2[i].status = 0;
                                    this.conversations2[i].timeCount = this.timeCost;
                                    this.conversations2[i].isActive = true;
                                }
                            }
                        }
                    });
            },

            listenUpdateMessage() {
                Echo.private('groups.' + this.group.id)
                    .listen('UpdateMessage', (e) => {
                        if(e.type == -1){
                            this.listMessage.push(e);
                            this.note_message = true;
                        }else if(e.type == 0){
                            var i = 0; 
                            for(i = 0; i < this.conversations.length; i++){
                                if(this.conversations[i].conversation == e.conversation){
                                    this.conversations[i].message = e.message;
                                    this.conversations[i].status = 0;
                                    this.conversations[i].timeCount = this.timeCost;
                                    // this.conversations[i].isActive = true;
                                }
                            }
                        }else if(e.type == 1){
                            var i = 0; 
                            for(i = 0; i < this.conversations.length; i++){
                                if(this.conversations1[i].conversation == e.conversation){
                                    this.conversations1[i].message = e.message;
                                    this.conversations1[i].status = 0;
                                    this.conversations1[i].timeCount = this.timeCost;
                                    // this.conversations1[i].isActive = true;
                                }
                            }
                        }else if(e.type == 2){
                            var i = 0; 
                            for(i = 0; i < this.conversations.length; i++){
                                if(this.conversations2[i].conversation == e.conversation){
                                    this.conversations2[i].message = e.message;
                                    this.conversations2[i].status = 0;
                                    this.conversations2[i].timeCount = this.timeCost;
                                    // this.conversations2[i].isActive = true;
                                }
                            }
                        }
                    });
            },
            listenAddConversation() {
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
            listenActiveConversation() {
                Echo.private('groups.' + this.group.id)
                    .listen('ActiveConversation', (e) => {
                        var self = this;
                        if(e.type == 0){
                            this.inActiveConversation(e);
                            this.conversations[e.conversation-1].timeCount = this.timeCost;
                            this.conversations[e.conversation-1].isActive = true;
                            this.conversations[e.conversation-1].status = 0;
                            this.conversations[e.conversation-1].userActive = e.user.name;
                            clearInterval(this.conversations[e.conversation-1].timeDown);
                            this.conversations[e.conversation-1].timeDown = setInterval(function(){
                                self.funcCount(self.conversations[e.conversation-1]);
                            }, 1000);

                        }else if(e.type == 1){
                            this.inActiveConversation(e);
                            this.conversations1[e.conversation-1].timeCount = this.timeCost;
                            this.conversations1[e.conversation-1].isActive = true;
                            this.conversations1[e.conversation-1].status = 0;
                            this.conversations1[e.conversation-1].userActive = e.user.name;
                            clearInterval(this.conversations1[e.conversation-1].timeDown);
                            this.conversations1[e.conversation-1].timeDown = setInterval(function(){
                                self.funcCount(self.conversations1[e.conversation-1]);
                            }, 1000);
                        }else if(e.type == 2){
                            this.inActiveConversation(e);
                            this.conversations2[e.conversation-1].timeCount = this.timeCost;
                            this.conversations2[e.conversation-1].isActive = true;
                            this.conversations2[e.conversation-1].status = 0;
                            this.conversations2[e.conversation-1].userActive = e.user.name;
                            clearInterval(this.conversations2[e.conversation-1].timeDown);
                            this.conversations2[e.conversation-1].timeDown = setInterval(function(){
                                self.funcCount(self.conversations2[e.conversation-1]);
                            }, 1000);
                        }
                    });
            },
            listenStatusConversation() {
                Echo.private('groups.' + this.group.id)
                    .listen('ChangeStatusConversation', (e) => {
                        if(e.type == 0){
                            this.conversations[e.conversation-1].status = e.status;
                        }else if(e.type == 1){
                            this.conversations1[e.conversation-1].status = e.status;
                        }else if(e.type == 2){
                            this.conversations2[e.conversation-1].status = e.status;
                        }
                        this.checkStatusGroup();
                    });
            },
            listenAutoSaveConversation() {
                Echo.private('groups.' + this.group.id)
                    .listen('UpdateMessage', (e) => {
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

            funcCount(obj){
                obj.timeCount -= 1;
                if(obj.timeCount == 0){
                    obj.isActive = false;
                    obj.userActive = "";
                    clearInterval(obj.timeDown);
                    obj.timeDown = false;
                }
            },

            change(cv, type){
                var self = this;
                if(cv.type == this.iuser.type || this.iuser.type == 0){
                    cv.timeCount = this.timeCost;
                }
                this.store(cv, type);
            },

            onChange(cv, type){
                var self = this;
                if(cv.isActive) return;
                if(cv.type == this.iuser.type || this.iuser.type == 0){
                    cv.timeCount = this.timeCost;
                    clearInterval(cv.timeDown);
                    cv.timeDown = setInterval(function(){
                        self.hideEditor(cv);
                    }, 1000);
                    this.sendActive(cv, this.iuser.id, type);
                }
                this.hideAllEditor();
                cv.showEditor = true;
            },

            hideEditor(obj){
                obj.timeCount -= 1;
                if(obj.timeCount == this.timeCost/2){
                    if(obj.showEditor){
                        axios.post('/conversation/save', {message: obj.message, group_id: this.group.id, type: obj.type, conversation: obj.conversation})
                        .then((response) => {
                            obj.status = 0;
                        });
                    }
                }
                if(obj.timeCount == 0){
                    obj.showEditor = false;
                    clearInterval(obj.timeDown);
                    obj.timeDown = false;
                }
            },

            sendActive(cv, user, type){
                axios.post('/conversation/active', {conversation: cv, group_id: this.group.id, type: type, user: user})
                .then((response) => {
                    cv.status = 0;
                });
            },

            hideAllEditor(){
                if(this.iuser.type == 0){
                    var i = 0; 
                    var _sefl = this;
                    for(i = 0; i < this.conversations.length; i++){
                        if(this.conversations[i].showEditor){
                            this.conversations[i].status = 0;
                            this.conversations[i].showEditor = false;
                            axios.post('/conversation/save', {message: this.conversations[i].message, group_id: this.group.id, type: this.conversations[i].type, conversation: this.conversations[i].conversation})
                            .then((response) => {
                            });
                        }

                        if(this.conversations1[i].showEditor){
                            this.conversations1[i].status = 0;
                            this.conversations1[i].showEditor = false;
                            axios.post('/conversation/save', {message: this.conversations1[i].message, group_id: this.group.id, type: this.conversations1[i].type, conversation: this.conversations1[i].conversation})
                            .then((response) => {
                            });
                        }

                        if(this.conversations2[i].showEditor){
                            this.conversations2[i].status = 0;
                            this.conversations2[i].showEditor = false;
                            axios.post('/conversation/save', {message: this.conversations2[i].message, group_id: this.group.id, type: this.conversations2[i].type, conversation: this.conversations2[i].conversation})
                            .then((response) => {
                            });
                        }
                    }
                }else if(this.iuser.type == 1){
                    var i = 0;
                    for(i = 0; i < this.conversations1.length; i++){
                        if(this.conversations1[i].showEditor){
                            this.conversations1[i].status = 0;
                            this.conversations1[i].showEditor = false;
                            axios.post('/conversation/save', {message: this.conversations1[i].message, group_id: this.group.id, type: this.conversations1[i].type, conversation: this.conversations1[i].conversation})
                            .then((response) => {
                            });
                        }
                    }
                }else if(this.iuser.type == 2){
                    var i = 0;
                    for(i = 0; i < this.conversations2.length; i++){
                        if(this.conversations2[i].showEditor){
                            this.conversations2[i].status = 0;
                            this.conversations2[i].showEditor = false;
                            axios.post('/conversation/save', {message: this.conversations2[i].message, group_id: this.group.id, type: this.conversations2[i].type, conversation: this.conversations2[i].conversation})
                            .then((response) => {
                            });
                        }
                    }
                }
            },

            addConversation(){
                if(this.iuser.type == 0){
                    axios.post('/group/' + this.group.id + '/addConversation', {group_id: this.group.id, type: this.iuser.type})
                            .then((response) => {
                                var i;
                                for(i = 0; i < response.data.length; i++){
                                    response.data[i].showEditor = false;
                                    response.data[i].status = 0;
                                    if(response.data[i].type == 0){
                                        this.conversations.push(response.data[i]);
                                    }else if(response.data[i].type == 1){
                                        this.conversations1.push(response.data[i]);
                                    }else{
                                        this.conversations2.push(response.data[i]);
                                    }
                                }
                                this.checkStatusGroup();
                            });
                }
            },

            changeStatusConversation(cv, obj, status){
                if(cv.isActive || cv.showEditor) return;
                cv.status = 1 - cv.status;
                axios.post('/conversation/' + this.group.id + '/change-status', {cv_id: cv.id, statusType: obj, status: cv.status})
                        .then((response) => {
                            this.checkStatusGroup();
                        });
            },

            checkStatusGroup(){
                var i;
                for(i = 0; i < this.conversations.length; i++){
                    if(this.conversations[i].status == 0){
                        this.done = false;
                        return false;
                    }
                    if(this.conversations1[i].status == 0){
                        this.done = false;
                        return false;
                    }
                    if(this.conversations2[i].status == 0){
                        this.done = false;
                        return false;
                    }
                }

                this.done = true;
                return false;
            },

            inActiveConversation(obj){
                var i = 0;
                // check 0
                for(i = 0; i < this.conversations.length; i++){
                    if(this.conversations[i].isActive){
                        if(this.conversations[i].userActive == obj.user.name){
                            this.conversations[i].status = 0;
                            this.conversations[i].isActive = false;
                            this.conversations[i].userActive = "";
                            clearInterval(this.conversations[i].timeDown);
                        }
                    }
                }
                // check 1
                for(i = 0; i < this.conversations1.length; i++){
                    if(this.conversations1[i].isActive){
                        if(this.conversations1[i].userActive == obj.user.name){
                            this.conversations1[i].status = 0;
                            this.conversations1[i].isActive = false;
                            this.conversations1[i].userActive = "";
                            clearInterval(this.conversations1[i].timeDown);
                        }
                    }
                }
                // check 2
                for(i = 0; i < this.conversations2.length; i++){
                    if(this.conversations2[i].isActive){
                        if(this.conversations2[i].userActive == obj.user.name){
                            this.conversations2[i].status = 0;
                            this.conversations2[i].isActive = false;
                            this.conversations2[i].userActive = "";
                            clearInterval(this.conversations2[i].timeDown);
                        }
                    }
                }
            }
        }
    }
</script>
