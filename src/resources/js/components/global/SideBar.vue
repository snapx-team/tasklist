<template>
    <div class="bg-gray-100 overflow-hidden shadow-lg h-full transition duration-150 ease-in-out "
         :class=" !isSideBarOpen ? 'w-20': 'w-64' ">

        <div class="text-center text-white font-semibold flex justify-between items-center relative z-10 bg-purple-700">
            <p v-if="isSideBarOpen" class="px-8"> Tasklist Plugin</p>
            <div @click="toggleSidebar()"
                 class="p-4 cursor-pointer transition duration-300 ease-in-out rounded hover:bg-purple-800 hover:text-purple-200`"
                 :class=" { 'flex-auto': !isSideBarOpen  }">
                <i class="fas fa-bars"></i>
            </div>
        </div>

        <hr/>

        <div class="py-4 transition duration-150 ease-in-out mt-3"
             :class=" !isSideBarOpen ? 'px-0': 'px-8' ">
            <div class="text-center">
                <i class="fas fa-hard-hat fa-3x p-3 text-indigo-800"></i>
                <div v-if="isSideBarOpen">
                    <h1 class="text-2xl tracking-wide text-indigo-800 font-bold my-0">
                        XGUARD TASKLIST </h1>
                    <p class="text-xs text-indigo-800">
                        View And Manage Job Site Task Lists</p>

                </div>
            </div>

            <nav class="mt-8">
                <h3 class="font-semibold text-gray-600 uppercase tracking-wide"
                    :class=" !isSideBarOpen ? 'text-center text-xs': 'text-left text-sm' ">General</h3>

                <div class="mt-2 -mx-3 pb-2" v-if="$role === 'admin'">
                    <router-link :to="{ path: '/tasklist/admin' }"
                                 class="flex items-center px-3 py-2 rounded-lg"
                                 :class="!isSideBarOpen ? 'mx-6 bg-gray-600 hover:bg-gray-500 justify-center': 'justify-between hover:bg-gray-200' ">
                        <span v-if="isSideBarOpen" class="text-sm font-medium text-gray-900"><i
                            class="fas fa-briefcase mr-2"></i>Admin Page</span>
                        <div v-else>
                            <i class="text-white m-2 fas fa-briefcase"></i>
                        </div>
                    </router-link>
                </div>

                <div class="-mx-3 pb-2">
                    <router-link :to="{ path: '/tasklist' }"
                                 class="flex items-center px-3 py-2 rounded-lg"
                                 :class="!isSideBarOpen ? 'mx-6 bg-gray-600 hover:bg-gray-500 justify-center' : 'justify-between hover:bg-gray-200' ">
                        <span v-if="isSideBarOpen" class="text-sm font-medium text-gray-900"><i
                            class="mr-2 fas fa-hard-hat"></i>Dashboard</span>
                        <div v-else><i class="text-white m-2 fas fa-hard-hat"></i></div>
                    </router-link>
                </div>

                <div class="-mx-3 pb-2">
                    <router-link :to="{ path: '/tasklist/user-profile' }"
                                 class="flex items-center px-3 py-2 rounded-lg"
                                 :class="!isSideBarOpen ? 'mx-6 bg-gray-600 hover:bg-gray-500 justify-center' : 'justify-between hover:bg-gray-200' ">
                        <span v-if="isSideBarOpen" class="text-sm font-medium text-gray-900"><i
                            class="mr-2 fas fa-user-cog"></i>User Profile</span>
                        <div v-else><i class="text-white m-2 fas fa-user-cog"></i></div>
                    </router-link>
                </div>

                <hr/>

                <div class="space-y-2 mt-8 " :class=" !isSideBarOpen ? 'text-center text-xl': 'text-left text-sm' ">
                    <a href="/"
                       class="text-xs text-gray-600 hover:text-gray-800 transition duration-300 ease-in-out focus:outline-none">
                        <i class="fas fa-sign-out-alt"></i>
                        <span v-if="isSideBarOpen" class="ml-1 font-bold">EXIT TASKLIST PLUGIN</span>
                    </a>
                </div>
            </nav>
        </div>
    </div>
</template>
<script>
import Avatar from "../global/Avatar.vue";

export default {

    inject: ["eventHub"],

    components: {
        Avatar,
    },

    data() {
        return {
            isSideBarOpen: true,
        };
    },

    mounted() {
        if (this.$device.mobile || this.$device.ios || this.$device.android || window.innerWidth < 1650) {
            this.isSideBarOpen = false;
        }
    },

    methods: {
        toggleSidebar() {
            this.isSideBarOpen = !this.isSideBarOpen;
            this.eventHub.$emit("check-pane-size:contract");
            this.eventHub.$emit("check-pane-size:jobSite");
        },
    }
};
</script>
