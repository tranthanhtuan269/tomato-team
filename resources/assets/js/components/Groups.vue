<template>
    <div>
        <group-chat v-for="group in groups" :group="group" :iuser="user" :key="group.id"></group-chat>
    </div>
</template>

<script>
    export default {
        props: ['initialGroups', 'user'],

        data() {
            return {
                groups: []
            }
        },

        mounted() {
            this.groups = this.initialGroups;
            this.iuser = this.user;
            //this.adminMessage = this.adminMessage;
            //this.group1Message = this.group1Message;
            //this.group2Message = this.group2Message;


            Bus.$on('groupCreated', (group) => {
                this.groups.push(group);
            });

            this.listenForNewGroups();
        },

        methods: {
            listenForNewGroups() {
                Echo.private('users.' + this.user.id)
                    .listen('GroupCreated', (e) => {
                        this.groups.push(e.group);
                    });
            }
        }
    }
</script>
