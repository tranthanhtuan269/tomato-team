<template>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Notification <span class="caret"></span>
        <span class="num-group" v-if="message > 0">{{ message }}</span>
        </a>
        <ul class="dropdown-menu">
            <li v-for="mes in listMessages" class="messageNotification">
                <a v-bind:href="'/groups/'+ mes.link" v-html="mes.data"></a>
            </li>
        </ul>
    </li>
</template>

<script>
    export default {
        props: ['user', 'baseUrl'],

        data() {
            return {
                message: 0,
                listMessages: []
            }
        },

        mounted() {
            Bus.$on('groupCreated', (group) => {
                console.log('Bus run');
            });

            this.listenCreateGroup();
            this.listenUpdateStatusGroup();

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
                        this.message++;
                        var data = {
                            'link' : e.group.id,
                            'data' : 'Bạn đã được thêm vào group <b>' + e.group.name + '</b>.'
                        }
                        this.listMessages.push(data);
                    });
            },

            listenUpdateStatusGroup() {
                Echo.private('users.' + this.user)
                    .listen('GroupUpdated', (e) => {
                        this.message++;
                        var data = {
                            'link' : e.group.id,
                            'data' : 'Group <b>' + e.group.name + '</b> đã được thay đổi.'
                        }
                        this.listMessages.push(data);
                    });
            },
        }
    }
</script>
