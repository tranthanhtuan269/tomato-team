<template>
    <div class="panel panel-default">
        <div class="panel-heading">Create Group</div>
        <div class="panel-body">
            <form>
                <div class="form-group">
                    <label for="exampleInputEmail1">Group Name</label>
                    <input class="form-control" type="text" v-model="name" placeholder="Group Name">
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Source Team</label>
                    <select v-model="users" class="form-control" multiple>
                        <option v-for="user in initialUsers" :value="user.id">
                            {{ user.name }}
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Target Team</label>
                    <select v-model="users2" class="form-control" multiple>
                        <option v-for="user in initialUsers" :value="user.id">
                            {{ user.name }}
                        </option>
                    </select>
                </div>
            </form>
        </div>
        <div class="panel-footer text-center">
            <button type="submit" @click.prevent="createGroup" class="btn btn-primary">Create Group</button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['initialUsers'],

        data() {
            return {
                name: '',
                users: [],
                users2: []
            }
        },

        methods: {
            createGroup() {
                axios.post('/groups', {name: this.name, users: this.users, users2: this.users2})
                .then((response) => {
                    this.name = '';
                    this.users = [];
                    this.users2 = [];
                    Bus.$emit('groupCreated', response.data);
                });
            }
        }
    }
</script>
