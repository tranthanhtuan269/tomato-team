
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.Bus = new Vue();

// import wysiwyg from "vue-wysiwyg";
import 'vue-snotify/styles/material.css'
import Snotify, { SnotifyPosition } from 'vue-snotify'; // 1. Import Snotify

import VueChatScroll from 'vue-chat-scroll'
Vue.use(VueChatScroll)

// var config = {

//   hideModules: { "image": true, "removeFormat" : true, "code" : true },
//   // limit content height if you wish. If not set, editor size will grow with content.
//   maxHeight: "100%",

//   // set to 'true' this will insert plain text without styling when you paste something into the editor.
//   forcePlainTextOnPaste: true
// }

// Vue.use(wysiwyg, config);
Vue.use(Snotify, {
  toast: {
    position: SnotifyPosition.centerCenter
  }
});

import VueQuillEditor from 'vue-quill-editor'

// require styles
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

Vue.use(VueQuillEditor);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example', require('./components/Example.vue'));
Vue.component('groups', require('./components/Groups.vue'));
Vue.component('create-group', require('./components/CreateGroup.vue'));
Vue.component('edit-group', require('./components/EditGroup.vue'));
Vue.component('group-chat', require('./components/GroupChat.vue'));
Vue.component('notification', require('./components/Notification.vue'));
Vue.component('vue-import', require('./components/VueImport.vue'));
Vue.component('message', require('./components/Message.vue'));

const app = new Vue({
    el: '#app'
});
