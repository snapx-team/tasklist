<template>
    <div>
        <div :class="`flex justify-between p-2 bg-${color}-800`">
            <div v-if="type === 'contract'" class="leading-5">
                <h1 class="text-white">Global Contract Tasks</h1>
                <small class="text-gray-400 text-xs"> Tasks for all job sites</small>
            </div>

            <div v-else class="leading-5">
                <h1 class="text-white">Job Site Tasks</h1>
                <small class="text-gray-400 text-xs"> Job site specific tasks</small>
            </div>

            <div v-if="type==='jobSite'">
                <button @click="togglePane"
                        class="focus:outline-none flex flex-col items-center text-gray-400 hover:text-gray-500 transition duration-150 ease-in-out pl-8"
                        type="button">
                    <i class="fas fa-times"></i>
                    <span
                        class="text-xs font-semibold text-center leading-3 uppercase p-1">Esc</span>
                </button>
            </div>
        </div>
        <div>
            <div>
                <div class="shadow-xl rounded-lg bg-gray-100 w-full relative"
                     :class=" showForm? 'formHeightOpen': 'formHeightClosed'  ">
                    <div class="text-center my-2">
                        <h2 class="font-semibold overflow-ellipsis overflow-hidden whitespace-nowrap px-2">
                            {{ title }}</h2>
                        <p class="text-gray-500 overflow-ellipsis overflow-hidden whitespace-nowrap px-2">
                            {{ subTitle }} </p>
                    </div>


                    <transition name="fade">
                        <div v-if="!showForm" class="mx-8 flex justify-center">
                            <button
                                v-if="!showForm"
                                @click="showForm = true"
                                :class="`w-10 h-10 bg-${color}-700 rounded-full hover:bg-${color}-800 mouse transition ease-in duration-200 focus:outline-none absolute`">
                                <i class="fas fa-plus text-white"></i>
                            </button>
                        </div>
                    </transition>

                    <transition name="fade">
                        <div v-if="showForm" class="border-t p-2">


                            <div class="flex mb-2">
                                <div class="bg-gray-300 rounded-l-lg">
                                    <i class="fas fa-tasks text-white mx-3 mt-3 text-sm"></i>
                                </div>
                                <input v-model="description" placeholder="new task to do..." type="text"
                                       class="h-10 px-3 py-3 placeholder-gray-400 text-gray-800 rounded-r-lg border border-gray-200 w-full pr-10 outline-none text-md leading-4">
                            </div>


                            <div class="flex">

                                <date-picker v-if="isRecurring"
                                             type="time"
                                             v-model="selectedTime"
                                             :minute-step="5"
                                             format="HH:mm"
                                             value-type="YYYY-MM-DD HH:mm:SS"
                                             placeholder="HH:mm"
                                             class="w-12"
                                ></date-picker>

                                <date-picker v-else
                                             type="datetime"
                                             v-model="selectedTime"
                                             :minute-step="5"
                                             format="YYYY-MM-DD HH:mm"
                                             value-type="YYYY-MM-DD HH:mm:SS"
                                             placeholder="YYYY-MM-DD HH:mm"
                                             class="w-12"
                                ></date-picker>
                                <div class="px-2 flex items-center">
                                    <button
                                        v-if="isRecurring"
                                        v-for="day in daysOfWeek"
                                        @click="toggleDayOfWeekSelection(day)"
                                        :class="isDaySelected(day)? 'bg-blue-500  hover:bg-blue-600': 'bg-gray-400  hover:bg-gray-500'"
                                        class=" text-white ml-0.5 w-6 h-6 text-xs font-semibold rounded-full mouse transition ease-in duration-75 focus:outline-none">
                                        {{ day.name }}
                                    </button>

                                    <button
                                        @click="toggleRecurring()"
                                        class=" text-white ml-1 w-7 h-7 text-xs rounded-full mouse transition ease-in duration-75 focus:outline-none bg-yellow-500 hover:bg-yellow-600 transition ease-in duration-75">
                                        <span v-show="isRecurring"><i class="fas fa-calendar text-white"></i></span>
                                        <span v-show="!isRecurring"><i class="fas fa-redo text-white"></i></span>
                                    </button>

                                </div>
                            </div>

                            <div class=" flex border-t mt-2 text-gray-500 text-sm">
                                <div v-if="isRecurring && selectedTime && selectedDaysOfWeek.length > 0">
                                    <span class="font-semibold ">Recurring:</span>
                                    <span v-if="selectedDaysOfWeek.length === 7">daily</span>
                                    <span v-else v-for="(day, index) in selectedDaysOfWeek">
                                            <span>{{ day.lowerCaseName }}</span><span
                                        v-if="index !== Object.keys(selectedDaysOfWeek).length - 1">, </span>
                                        </span>
                                    <span> at {{ formattedSelectedTime }} </span>
                                </div>
                                <div v-else-if="isRecurring && selectedDaysOfWeek.length === 0">
                                    <p class="font-semibold ">No days of week selected</p>
                                </div>
                                <div v-else-if="!isRecurring && selectedTime">
                                    <span class="font-semibold pr-1">Once:</span>
                                    <span>at {{ selectedTime }}</span>
                                </div>
                                <div v-else>
                                    <p class="font-semibold pr-1">No time selected</p>
                                </div>
                            </div>

                            <div class="flex justify-center mt-2 absolute text-center w-full">
                                <button
                                    @click="clearTask()"
                                    class="w-10 h-10 bg-yellow-700 rounded-full hover:bg-yellow-800 mouse transition ease-in duration-200 focus:outline-none mx-1">
                                    <i class="fas fa-times text-white"></i>
                                </button>
                                <button
                                    @click="showForm = false"
                                    :class="`w-10 h-10 bg-${color}-700 rounded-full hover:bg-${color}-800 mouse transition ease-in duration-200 focus:outline-none mx-1`">
                                    <i class="fas fa-chevron-up text-white"></i>
                                </button>
                                <button
                                    @click="saveTask()"
                                    class="w-10 h-10 bg-blue-700 rounded-full hover:bg-blue-800 mouse transition ease-in duration-200 focus:outline-none mx-1">
                                    <i class="fas fa-check text-white"></i>
                                </button>
                            </div>

                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </div>

</template>
<script>

import JobSiteCard from "./JobSiteCard"
import {axiosCalls} from "../../../mixins/axiosCallsMixin";
import LoadingAnimation from "../../global/LoadingAnimation";
import Badge from "../../global/Badge.vue";
import moment from 'moment';

export default {
    inject: ["eventHub"],
    components: {Badge, JobSiteCard, LoadingAnimation},
    mixins: [axiosCalls],

    props: {
        selectedItem: {
            type: Object,
        },
        type: {
            type: String,
        },
        color: {
            type: String, default: "green",
        },
        title: {
            type: String,
        },
        subTitle: {
            type: String,
        },
        daysOfWeek: {}
    },

    data() {
        return {
            selectedDaysOfWeek: [],
            showForm: false,
            selectedTime: null,
            isRecurring: true,
            description: null,
        }
    },

    methods: {

        togglePane() {
            this.eventHub.$emit("toggle-job-site-task-pane");
        },
        toggleDayOfWeekSelection(day) {
            const selectedIndex = this.selectedDaysOfWeek.findIndex(d => d.id === day.id);
            if (selectedIndex !== -1) {
                this.selectedDaysOfWeek.splice(selectedIndex, 1);
            } else {
                this.selectedDaysOfWeek.push(day);
                this.selectedDaysOfWeek.sort((a, b) => a.id - b.id); // Sort the array by id
            }
        },

        isDaySelected(day) {
            return this.selectedDaysOfWeek.some(d => d.id === day.id);
        },

        toggleRecurring() {
            this.selectedTime = null
            this.isRecurring = !this.isRecurring;
        },
        saveTask() {
            this.asyncCreateTask(this.typeData).then(() => {
                if (this.type === 'contract') {
                    this.eventHub.$emit("reload-tasks:contract");
                } else if (this.type === 'jobSite') {
                    this.eventHub.$emit("reload-tasks:jobSite");
                }
            });
        },
        clearTask() {
            this.selectedDaysOfWeek = [];
            this.selectedTime = null;
            this.description = '';
            this.isRecurring =  true;
        }
    },
    computed: {
        typeData() {
            if (this.type === "contract") {
                return {
                    contractId: this.selectedItem.id,
                    jobSiteAddressId: null,
                    selectedDaysOfWeek: this.selectedDaysOfWeek,
                    selectedTime: this.selectedItem,
                    isRecurring: this.isRecurring,
                    description: this.description,
                    time:this.selectedTime
                }
            } else if (this.type === "jobSite") {
                return {
                    contractId: this.selectedItem.contract.id,
                    jobSiteAddressId: this.selectedItem.contractJobSite.id,
                    selectedDaysOfWeek: this.selectedDaysOfWeek,
                    selectedTime: this.selectedItem,
                    isRecurring: this.isRecurring,
                    description: this.description,
                    time:this.selectedTime
                }
            }
        },

        formattedSelectedTime() {
            return moment(this.selectedTime).format('HH:mm');
        },
    },
}
</script>

<style>

.formHeightClosed {
    height: 80px;
    transition: height 0.3s;
}

.formHeightOpen {
    height: 210px;
    transition: height 0.3s;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter,
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
