<template>
    <div>
        list group
    </div>
</template>

<script>
    export default {
        props: ['initialUsers'],

        data() {
            return {
                name: '',
                users: []
            }
        },

        methods: {
            createGroup() {
                axios.post('/groups', {name: this.name, users: this.users})
                .then((response) => {
                    this.name = '';
                    this.users = [];
                    Bus.$emit('groupCreated', response.data);
                });
            }
        }
    }
</script>
