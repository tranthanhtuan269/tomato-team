<template>
<div>
    <div class="alert alert-success" role="alert" v-if="updated_success">Sửa group thành công!</div>
    <div class="alert alert-danger" role="alert" v-if="error_name">Tên group không nên để trống!</div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">Edit Group: <span v-html="group.name"></span></div>
            <div class="panel-body">
                <form>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Group Name</label>
                        <input class="form-control" type="text" v-model="group.name" placeholder="Group Name">
                    </div>
                    <div class="form-group">
                        <ul class="list-group col-sm-6">
                            <li class="list-group-item list-group-item-info">Source team</li>
                            <li class="list-group-item" v-for="user in usersSelected" :value="user.id" v-on:click="rollbackUserToSourceTeam(user)">{{ user.name }}</li>
                        </ul>
                    </div>
                    <div class="form-group">
                        <ul class="list-group col-sm-6">
                            <li class="list-group-item list-group-item-info">Target team</li>
                            <li class="list-group-item" v-for="user in users2Selected" :value="user.id" v-on:click="rollbackUserToTargetTeam(user)">{{ user.name }}</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="panel-footer text-center">
                <button type="submit" @click.prevent="updateGroup" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">List Users</div>
            <div class="panel-body">
                <ul class="list-group col-sm-6">
                    <li class="list-group-item list-group-item-info">Source team</li>
                    <li class="list-group-item" v-for="user in onlineUsersSource" :value="user.id" v-on:click="addUserToSourceTeam(user)" v-if="user.busy_job == 0 || showAllUser">{{ user.name }}</li>
                </ul>
                <ul class="list-group col-sm-6">
                    <li class="list-group-item list-group-item-info">Target team</li>
                    <li class="list-group-item" v-for="user in onlineUsersTarget" :value="user.id" v-on:click="addUserToTargetTeam(user)" v-if="user.busy_job == 0 || showAllUser">{{ user.name }}</li>
                </ul>
            </div>
            <div class="panel-footer">
                <div class="btn btn-primary" v-on:click="changeShowAll(true)" v-if="!showAllUser">Show all</div>
                <div class="btn btn-primary" v-on:click="changeShowAll(false)" v-if="showAllUser">Show available</div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
    export default {
        props: ['group', 'joined'],
        data() {
            return {
                name: '',
                updated_success: false,
                error_name: false,
                error_source: false,
                error_target: false,
                users: [],
                usersSelected: [],
                onlineUsersSource: [],
                onlineUsersTarget: [],
                users2: [],
                users2Selected: [],
                showAllUser: false
            }
        },

        mounted() {
            this.addUserJoined(this.joined);

            Bus.$on('online_users', (users) => {
                var self = this;
                users = users.filter(function (item) {
                    if(item.languages == 0 && !self.checkExistUser(self.onlineUsersSource, item && !self.checkExistUser(self.usersSelected, item))){
                        self.onlineUsersSource.push(item);
                    }

                    if(item.languages == 1 && !self.checkExistUser(self.onlineUsersTarget, item && !self.checkExistUser(self.users2Selected, item))){
                        self.onlineUsersTarget.push(item);
                    }
                });
            });

            Bus.$on('user_join', (user) => {
                if(user.languages == 0){
                    this.onlineUsersSource.push(user);
                }

                if(user.languages == 1){
                    this.onlineUsersTarget.push(user);
                }
            });

            Bus.$on('user_leave', (user) => {
                this.onlineUsersSource = this.onlineUsersSource.filter(function (item) {
                    return user != item.id;
                });
                this.onlineUsersTarget = this.onlineUsersTarget.filter(function (item) {
                    return user != item.id;
                });
            });
        },

        methods: {
            changeShowAll(status){
                this.showAllUser = status;
            },
            checkExistUser(arr, user){
                var found = arr.some(function (el) {
                    return el.id === user.id;
                });
                if (!found) return false;
                return true;
            },
            addUserJoined(joined){
                var self = this;
                joined = joined.filter(function (item) {
                    if(item.languages == 0){
                        self.usersSelected.push(item);
                    }
                    if(item.languages == 1){
                        self.users2Selected.push(item);
                    }
                });
            },

            updateGroup() {
                if(this.group.name == ''){ 
                    this.error_name = true; 
                    return;
                }else{ 
                    this.error_name = false;
                }

                axios.put('/groups/' + this.group.id, {name: this.group.name, users: this.usersSelected, users2: this.users2Selected})
                .then((response) => {
                    this.name = '';
                    Bus.$emit('groupUpdated', response.data);
                    this.updated_success = true;
                });
            },
            addUserToSourceTeam(user){
                this.onlineUsersSource = this.onlineUsersSource.filter(function (item) {
                    return user.id != item.id;
                });
                this.onlineUsersTarget = this.onlineUsersTarget.filter(function (item) {
                    return user.id != item.id;
                });
                this.users.push(user.id);
                this.usersSelected.push(user);
            },
            addUserToTargetTeam(user){
                this.onlineUsersSource = this.onlineUsersSource.filter(function (item) {
                    return user.id != item.id;
                });
                this.onlineUsersTarget = this.onlineUsersTarget.filter(function (item) {
                    return user.id != item.id;
                });
                this.users2.push(user.id);
                this.users2Selected.push(user);
            },
            rollbackUserToSourceTeam(user){
                this.onlineUsersSource.push(user);
                this.users = this.users.filter(function (item) {
                    return user.id != item;
                });
                this.usersSelected = this.usersSelected.filter(function (item) {
                    return user.id != item.id;
                });
            },
            rollbackUserToTargetTeam(user){
                this.onlineUsersTarget.push(user);
                this.users2 = this.users2.filter(function (item) {
                    return user.id != item;
                });
                this.users2Selected = this.users2Selected.filter(function (item) {
                    return user.id != item.id;
                });
            }
        }
    }
</script>
