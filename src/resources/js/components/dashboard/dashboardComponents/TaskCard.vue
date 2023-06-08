<template>
    <div class="rounded border-l-4 bg-white shadow-lg m-2 flex justify-between" :class="typeColor">
        <div class="p-2 flex-1">
            <h1 class="text-gray-700 py-2 px-4 my-2 bg-gray-100 rounded">
                {{ task.description }}
            </h1>
            <div class="flex items-center space-x-2">
                <div class="flex items-center">
                    <badge :name="task.isRecurring? 'Recurring':'Once'"></badge>
                </div>
                <div class="flex">
                    <p
                        v-if="task.isRecurring"
                        v-for="day in daysOfWeek"
                        :class="isDaySelected(day)? 'bg-blue-500': 'bg-gray-400'"
                        class=" text-white ml-0.5 p-1 font-semibold rounded-full mouse"
                        style="font-size: 8px">
                        {{ day.name }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600" v-if="!task.time">Time Undefined Error</p>
                    <p class="text-sm text-gray-600" v-else-if="!task.isRecurring">on
                        {{ task.time | moment("DD MMM, YYYY - HH:MM a") }}</p>
                    <p class="text-sm text-gray-600" v-else-if="task.isRecurring">at
                        {{ task.time | moment("HH:MM a") }}</p>
                </div>

            </div>
        </div>

        <div class="flex flex-col items-stretch">
            <button class="flex-1 p-2 bg-blue-300 text-white rounded-tr hover:bg-blue-400 text-sm">
                <i class="fas fa-edit"></i>
            </button>
            <button class="flex-1 p-2 bg-red-300 text-white rounded-br hover:bg-red-400 text-sm">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    </div>
</template>
<script>
import Badge from "../../global/Badge.vue";
import {axiosCalls} from "../../../mixins/axiosCallsMixin";

export default {
    name: 'taskCard',
    components: {Badge},
    mixins: [axiosCalls],

    props: {
        task: {
            type: Object,
        },
        type:{
            type: String
        },
        daysOfWeek: {
            type: Array
        },
    },

    computed: {
        typeColor() {
            if (this.type === "contract") {
                return 'border-purple-500';
            } else if (this.type === "jobSite") {
                return 'border-green-500';
            } else {
                return 'border-gray-500';
            }
        },
    },

    methods: {
        isDaySelected(day) {
            return this.task.taskRecurrence.some(d => d.dayOfWeekId === day.id);
        },
    }
}
</script>
