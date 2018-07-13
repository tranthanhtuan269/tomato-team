<template>
<div>
    <div class="alert alert-success" role="alert" v-if="created_success">Tạo group thành công!</div>
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
                    <li class="list-group-item" v-for="user in onlineUsers" :value="user.id" v-on:click="addUserToSourceTeam(user)">{{ user.name }}</li>
                </ul>
                <ul class="list-group col-sm-6">
                    <li class="list-group-item list-group-item-info">Target team</li>
                    <li class="list-group-item" v-for="user in onlineUsers" :value="user.id" v-on:click="addUserToTargetTeam(user)">{{ user.name }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
</template>

<script>
    export default {
        props: ['initialUsers1', 'initialUsers2'],

        data() {
            return {
                name: '',
                created_success: false,
                users: [],
                usersSelected: [],
                onlineUsers: [],
                users2: [],
                users2Selected: []
            }
        },

        created() {
            Bus.$on('online_users', (users) => {
                console.log(users);
                this.onlineUsers = users;
            });

            Bus.$on('user_join', (user) => {
                console.log(user);
            });

            Bus.$on('user_leave', (user) => {
                console.log(user);
            });
        },

        methods: {
            createGroup() {
                axios.post('/groups', {name: this.name, users: this.users, users2: this.users2})
                .then((response) => {
                    this.name = '';
                    Bus.$emit('groupCreated', response.data);
                    this.created_success = true;
                });
            },
            addUserToSourceTeam(user){
                this.onlineUsers = this.onlineUsers.filter(function (item) {
                    return user.id != item.id;
                });
                this.users.push(user.id);
                this.usersSelected.push(user);
            },
            addUserToTargetTeam(user){
                this.onlineUsers = this.onlineUsers.filter(function (item) {
                    return user.id != item.id;
                });
                this.users2.push(user.id);
                this.users2Selected.push(user);
            },
            rollbackUserToSourceTeam(user){
                this.onlineUsers.push(user);
                this.users = this.users.filter(function (item) {
                    return user.id != item;
                });
                this.usersSelected = this.usersSelected.filter(function (item) {
                    return user.id != item.id;
                });
            },
            rollbackUserToTargetTeam(user){
                this.onlineUsers.push(user);
                this.users2 = this.users2.filter(function (item) {
                    return user.id != item;
                });
                this.users2Selected = this.users2Selected.filter(function (item) {
                    return user.id != item.id;
                });
            },

        }
    }
</script>
