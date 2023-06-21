<template>
    <div ref="responsiveDiv" class="flex flex-col relative" style="height:803px">
        <div v-if="componentWidth > 400" class="flex flex-col" style="height:803px">
            <CreateTaskHeader :selected-item="selectedItem"
                              :color="typeData.color"
                              :title="typeData.title"
                              :sub-title="typeData.subTitle"
                              :type="type"
                              :days-of-week="daysOfWeek"/>

            <div class="overflow-auto pt-3">
                <TaskCard v-for="task in tasks"
                          :key="task.id"
                          :task="task"
                          :type="type"
                          :days-of-week="daysOfWeek"/>
            </div>
        </div>

        <div v-else class="w-full h-80 flex justify-center items-center">
            <div class="text-center">
                <div :class="`text-6xl text-indigo-300`">
                    <i class="fas fa-arrows-alt-h"></i>
                </div>
                <p class="text-gray-500 font-semibold text-sm mt-2">please expand pane</p>
            </div>
        </div>

        <div v-if="isLoadingTasks"
             class="absolute inset-0 flex items-center justify-center bg-gray-300 bg-opacity-50">
            <loading-animation :size="100" class="m-auto"></loading-animation>
        </div>

    </div>

</template>
<script>

import CityScapeBackground from "../../global/CityScapeBackground.vue";
import CreateTaskHeader from "./CreateTaskHeader.vue";
import TaskCard from "./TaskCard.vue";
import {axiosCalls} from "../../../mixins/axiosCallsMixin";
import LoadingAnimation from "../../global/LoadingAnimation.vue";

export default {
    components: {LoadingAnimation, TaskCard, CreateTaskHeader, CityScapeBackground},
    mixins: [axiosCalls],
    inject: ["eventHub"],

    watch: {
        selectedItem: function (newVal, oldVal) {
            this.getTasks();
        }
    },
    props: {
        type: {
            type: String,
        },
        selectedItem: {
            type: Object,
        },
    },
    data() {
        return {
            tasks: [],
            componentWidth: 0,
            isLoadingTasks: false,
            daysOfWeek: [
                {name: 'MO', id: 1, lowerCaseName: 'mon'},
                {name: 'TU', id: 2, lowerCaseName: 'tue'},
                {name: 'WE', id: 3, lowerCaseName: 'wed'},
                {name: 'TH', id: 4, lowerCaseName: 'thu'},
                {name: 'FR', id: 5, lowerCaseName: 'fri'},
                {name: 'SA', id: 6, lowerCaseName: 'sat'},
                {name: 'SU', id: 7, lowerCaseName: 'sun'}
            ],
        }
    },

    mounted() {
        this.getComponentWidth();
        this.getTasks();
        window.addEventListener('resize', this.getComponentWidth);
    },

    created() {
        this.eventHub.$on(`check-pane-size:` + this.type, () => {
            this.getComponentWidth();
        });
        this.eventHub.$on(`reload-tasks:` + this.type, () => {
            this.getTasks();
        });
    },

    beforeDestroy() {
        this.eventHub.$off(`check-pane-size:` + this.type);
        this.eventHub.$off(`reload-tasks:` + this.type);
        window.removeEventListener('resize', this.getComponentWidth);
    },

    methods: {
        getComponentWidth() {
            setTimeout(() => this.componentWidth = this.$refs.responsiveDiv.offsetWidth, 100);
        },
        getTasks() {
            this.isLoadingTasks = true
            if (this.type === "contract") {
                this.asyncGetGlobalContractTasks(this.typeData.contractId).then((data) => {
                    this.tasks = data;
                    this.isLoadingTasks = false;
                });
            } else if (this.type === "jobSite") {
                this.asyncGetJobSiteTasks(this.typeData.jobSiteAddressId).then((data) => {
                    this.tasks = data;
                    this.isLoadingTasks = false;
                });
            }
        }
    },

    computed: {
        typeData() {
            if (this.type === "contract") {
                return {
                    contractId: this.selectedItem.id,
                    jobSiteAddressId: null,
                    color: 'purple',
                    title: this.selectedItem.name,
                    subTitle: this.selectedItem.jobSiteType ? this.selectedItem.jobSiteType : 'Job site type undefined'
                }
            } else if (this.type === "jobSite") {
                return {
                    contractId: this.selectedItem.id,
                    jobSiteAddressId: this.selectedItem.contractJobSite.id,
                    color: 'green',
                    title: this.selectedItem.contractJobSite.address,
                    subTitle: this.selectedItem.contractJobSite.isPrimaryAddress ? 'Primary Address' : 'Secondary Address'
                }
            } else {
                return {
                    color: 'gray',
                    title: 'n/a',
                    subTitle: 'n/a'
                }
            }
        },
    },
}
</script>
