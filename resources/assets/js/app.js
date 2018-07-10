
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.Bus = new Vue();

import wysiwyg from "vue-wysiwyg";

var config = {

  // limit content height if you wish. If not set, editor size will grow with content.
  maxHeight: "500px",

  // set to 'true' this will insert plain text without styling when you paste something into the editor.
  forcePlainTextOnPaste: true
}

Vue.use(wysiwyg, config); // config is optional. more below
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example', require('./components/Example.vue'));
Vue.component('groups', require('./components/Groups.vue'));
Vue.component('create-group', require('./components/CreateGroup.vue'));
Vue.component('group-chat', require('./components/GroupChat.vue'));

const app = new Vue({
    el: '#app'
});
