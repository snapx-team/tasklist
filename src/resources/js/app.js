import Vue from 'vue';
import App from './App.vue';
import router from './router';
import 'vue-select/dist/vue-select.css';
import '@fortawesome/fontawesome-free/js/all.js';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import DatePicker from 'vue2-datepicker';
import VueMoment from 'vue-moment';
import moment from 'moment-timezone';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import axios from 'axios';
import device from 'vue-device-detector';
import VueCrontab from 'vue-crontab';
import * as VueGoogleMaps from 'vue2-google-maps';
import VueImg from 'v-img';

Vue.config.productionTip = false;

Vue.use(VueGoogleMaps, {
    load: {
        key: '',
        libraries: 'places'
    },
    installComponents: true
});
Vue.use(Toast, {
    transition: 'Vue-Toastification__fade',
    maxToasts: 20,
    newestOnTop: true
});
Vue.use(VueImg);
Vue.use(VueSweetalert2);
Vue.use(DatePicker);
Vue.use(VueMoment, {moment});
Vue.use(device);
Vue.use(VueCrontab);



let globalData = new Vue({
    data: {$role: 'employee'}
});
Vue.mixin({
    computed: {
        $role: {
            get: function () {
                return globalData.$data.$role;
            },
            set: function (newRole) {
                globalData.$data.$role = newRole;
            }
        },

        $tasklistIdSession: {
            get: function () {
                return globalData.$data.$tasklistId;
            },
            set: function (newEmployeeId) {
                globalData.$data.$tasklistId = newEmployeeId;
            }
        }
    }
});

new Vue({
    router,
    mounted() {
        axios.get('get-role-and-employee-id').then((data) => {
            this.$role = data.data['role'];
            this.$tasklistIdSession = data.data['tasklist_id'];
        });
    },
    render: (h) => h(App)
}).$mount('#app');
