import Vue from 'vue';
import Vuetify from 'vuetify';

import App from './components/app.vue';
import DeveloperList from './components/developer-list.vue';

Vue.use(Vuetify);

new Vue({
    el: '#app',
    components: {
        'app': App,
        'developer-list': DeveloperList
    }
});
