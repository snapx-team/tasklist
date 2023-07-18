<template>
    <div v-if="!this.loadingProfile">

        <div class="bg-gray-100 w-full h-64 absolute top-0 rounded-b-lg" style="z-index: -1"></div>

        <div class="flex flex-wrap p-4 pl-10">
            <h3 class="text-3xl text-gray-800 font-bold py-1 pr-8">User Profile</h3>
        </div>

        <div class="w-96 m-auto">

            <div class="bg-white p-3 border-t-4 border-indigo-400 shadow">
                <avatar :name="userName" :size="20" class="shadow m-auto"></avatar>
                <h1 class="text-gray-900 font-bold text-xl leading-8 mt-2 mb-3 text-center"> {{ this.userName }}</h1>
                <h3 class="text-gray-600 leading-6 px-1 ">General</h3>
                <ul class="bg-gray-100 text-gray-600 hover:text-gray-700 hover:shadow py-2 px-3 mt-3 divide-y rounded shadow-sm">
                    <li class="flex items-center py-3">
                        <span>Status</span>
                        <span class="ml-auto"><span
                            class="bg-green-500 py-1 px-2 rounded text-white text-sm">{{ this.status }}</span></span>
                    </li>
                    <li class="flex items-center py-3">
                        <span>Tasklist app member since</span>

                        <span class="ml-auto">{{ this.memberSince }}</span>
                    </li>
                </ul>

                <hr class=my-4>
                <h3 class="text-gray-600 leading-6 px-1">Preferences (from ERP)</h3>
                <ul class="bg-gray-100 text-gray-600 hover:text-gray-700 hover:shadow py-2 px-3 mt-3 divide-y rounded shadow-sm">
                    <li class="flex items-center py-3">
                        <span>Language</span>
                        <span class="ml-auto">{{ language }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import {axiosCalls} from "../../mixins/axiosCallsMixin";
import Avatar from "../global/Avatar";

export default {
    inject: ["eventHub"],

    components: {
        Avatar,
    },
    data() {
        return {
            userName: '',
            status: '',
            memberSince: '',
            language: '',
            loadingProfile: true,
        }
    },

    mixins: [axiosCalls],

    mounted() {
        this.getEmployeeProfileData();
    },

    methods: {
        getEmployeeProfileData() {
            this.eventHub.$emit("set-loading-state", true);
            this.asyncGetEmployeeProfile().then((data) => {
                this.userName = data.data.userName;
                this.status = data.data.userStatus;
                this.memberSince = data.data.userCreatedAt;
                this.language = data.data.language;
                this.loadingProfile = false;
                this.eventHub.$emit("set-loading-state", false);
            })
        },
    },
};
</script>
