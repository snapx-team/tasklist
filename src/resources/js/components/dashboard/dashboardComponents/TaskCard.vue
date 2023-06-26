<template>
    <div class="rounded border-l-4 bg-white shadow-lg m-2 flex justify-between" :class="typeColor">
        <div class="p-2 flex-1">
            <v
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
                        {{ task.time | moment("DD MMM, YYYY - HH:mm ") }}</p>
                    <p class="text-sm text-gray-600" v-else-if="task.isRecurring">at
                        {{ task.time }}</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-stretch">
            <button @click="editTask()"
                    class="flex-1 p-2 bg-blue-300 text-white rounded-tr hover:bg-blue-400 text-sm">
                <i class="fas fa-edit"></i>
            </button>
            <button @click="deleteTask()"
                    class="flex-1 p-2 bg-red-300 text-white rounded-br hover:bg-red-400 text-sm">
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
    inject: ["eventHub"],

    props: {
        task: {
            type: Object,
        },
        type: {
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
        deleteTask() {
            this.$swal({
                icon: 'warning',
                title: 'Are you sure you want to delete this?',
                text: this.task.description,
                showCancelButton: true,
                confirmButtonText: `Delete`,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return this.asyncDeleteTask(this.task.id).then(() => {
                        if (this.type === 'contract') {
                            this.eventHub.$emit("reload-tasks:contract");
                        } else if (this.type === 'jobSite') {
                            this.eventHub.$emit("reload-tasks:jobSite");
                        }
                    })
                        .catch((error) => {
                            this.$swal({
                                icon: 'warning',
                                title: 'Could not delete task',
                                text: error,
                                showCancelButton: true,
                                confirmButtonText: `Return To Dashboard`,
                            })
                        });
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    this.modalOpen = false;
                }
            });

        },

        editTask() {
            this.$swal({
                title: 'Edit Task Description',
                text: 'If you need to change the time or date, please delete this one and create a new task.',
                input: 'text',
                inputValue: this.task.description,
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Confirm Edit',
                showLoaderOnConfirm: true,
                preConfirm: (newDescription) => {
                    return this.asyncEditTask(this.task.id, newDescription).then(() => {
                        if (this.type === 'contract') {
                            this.eventHub.$emit("reload-tasks:contract");
                        } else if (this.type === 'jobSite') {
                            this.eventHub.$emit("reload-tasks:jobSite");
                        }
                    })
                        .catch((error) => {
                            this.$swal({
                                icon: 'warning',
                                title: 'Could not edit task',
                                text: error,
                                showCancelButton: true,
                                confirmButtonText: `Return To Dashboard`,
                            })
                        });
                },
                allowOutsideClick: () => !this.$swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    this.modalOpen = false;
                }
            })
        },
        isDaySelected(day) {
            return this.task.taskRecurrence.some(d => d.dayOfWeekId === day.id);
        },
    }
}
</script>
