import Vue from 'vue';
import Router from 'vue-router';
import Dashboard from '../components/dashboard/Dashboard';
import UserProfile from '../components/users/UserProfile';
import Admin from '../components/admin/Admin';

Vue.use(Router);

export default new Router({
    mode: 'history',

    routes: [
        {
            path: '*',
            redirect: 'tasklist/dashboard'
        },
        {
            path: '/tasklist/',
            redirect: 'tasklist/dashboard'
        },
        {
            name: 'dashboard',
            path: '/tasklist/dashboard',
            component: Dashboard
        },

        {
            path: '/tasklist/admin',
            component: Admin
        },

        {
            path: '/tasklist/user-profile',
            component: UserProfile
        }
    ]
});
