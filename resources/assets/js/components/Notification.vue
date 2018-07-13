<template>
    <span class="num-group" v-if="message > 0">{{ message }}</span>
</template>

<script>
    export default {
        props: ['user'],

        data() {
            return {
                message: 0
            }
        },

        mounted() {
            Bus.$on('groupCreated', (group) => {
                console.log('Bus run');
            });

            this.listenCreateGroup();

            Echo.join('chats')
                .here((users) => {
                    Bus.$emit('online_users', users);
                })
                .joining((user) => {
                    Bus.$emit('user_join', user);
                })
                .leaving((user) => {
                    Bus.$emit('user_leave', user.id);
                });
        },

        methods: {
            listenCreateGroup() {
                Echo.private('users.' + this.user)
                    .listen('GroupCreated', (e) => {
                        this.message++;
                    });
            }
        }
    }
</script>
