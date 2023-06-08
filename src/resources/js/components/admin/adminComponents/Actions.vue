<template>
    <div class="Actions">
        <h2 class="text-2xl font-semibold py-5">Actions</h2>

        <div class="flex flex-wrap bg-gray-100">
            <div class="w-full p-6" v-if="$role === 'admin'">
                <div @click="createEmployees()"
                     class="cursor-pointer bg-green-100 hover:bg-green-200 transition duration-300 ease-in-out border-b-4 border-green-500 rounded-lg shadow-xl p-5">
                    <div class="flex flex-row items-center">
                        <div class="flex-shrink pr-4">
                            <div class="rounded-full p-5 bg-green-500 text-center">
                                <i class="fa fa-user fa-2x fa-inverse w-8 h-7"></i>
                            </div>
                        </div>
                        <div class="flex-1 text-right md:text-center">
                            <h5 class="font-bold uppercase text-gray-800">Add Employee</h5>
                            <h3 class="text-sm text-gray-600">
                                {{ employeesLength }} employees total </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {eventNames} from "../../../enums/eventNames";

export default {

    inject: ["eventHub"],

    props: {
        employeesLength: {
            type: Number,
            default: 0,
        },
    },
    methods: {
        createEmployees() {
            this.eventHub.$emit(eventNames.createEmployees);
        },
    },
};
</script>


