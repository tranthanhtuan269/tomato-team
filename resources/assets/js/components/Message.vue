<template>
    <div class="message-item">
        <li class="list-group-item"><slot></slot></li>
        <small class="badge pull-right badge-danger">{{ user }}</small>
    </div>
</template>

<script>
    export default {
        props: ['user'],
        mounted() {
            
        }
    }
</script>
