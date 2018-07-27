<template>
<div>
    <div class="alert alert-success" role="alert" v-if="created_success">Tạo group thành công!</div>
    <div class="alert alert-danger" role="alert" v-if="error_name">Tên group không nên để trống!</div>
    <div class="alert alert-danger" role="alert" v-if="error_source">Bạn chưa thêm thành viên cho source team!</div>
    <div class="alert alert-danger" role="alert" v-if="error_target">Bạn chưa thêm thành viên cho target team!</div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">Create Group</div>
            <div class="panel-body">
                <form>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Group Name</label>
                        <input class="form-control" type="text" v-model="name" placeholder="Group Name">
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
                <button type="submit" @click.prevent="createGroup" class="btn btn-primary">Create Group</button>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">List Users</div>
            <div class="panel-body">
                <ul class="list-group col-sm-6">
                    <li class="list-group-item list-group-item-info">Source team</li>
                    <li class="list-group-item" v-for="user in onlineUsersSource" :value="user.id" v-on:click="addUserToSourceTeam(user)" v-if="user.busy_job == 0 || showAllUser">{{ user.id }}</li>
                </ul>
                <ul class="list-group col-sm-6">
                    <li class="list-group-item list-group-item-info">Target team</li>
                    <li class="list-group-item" v-for="user in onlineUsersTarget" :value="user.id" v-on:click="addUserToTargetTeam(user)" v-if="user.busy_job == 0 || showAllUser">{{ user.id }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
</template>

<script>
    export default {
        data() {
            return {
                name: '',
                created_success: false,
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
            Bus.$on('online_users', (users) => {
                var self = this;
                users = users.filter(function (item) {
                    if(item.languages == 0){
                        self.onlineUsersSource.push(item);
                    }

                    if(item.languages == 1){
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
            createGroup() {
                if(this.name == ''){ 
                    this.error_name = true; 
                    return;
                }else{ 
                    this.error_name = false;
                }

                // if(this.users.length == 0){
                //     this.error_source = true; 
                //     return;
                // }else{
                //     this.error_source = false;
                // }

                // if(this.users2.length == 0){
                //     this.error_target = true; 
                //     return;
                // }else{
                //     this.error_target = false;
                // }

                axios.post('/groups', {name: this.name, users: this.users, users2: this.users2})
                .then((response) => {
                    this.name = '';
                    Bus.$emit('groupCreated', response.data);
                    this.created_success = true;
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
