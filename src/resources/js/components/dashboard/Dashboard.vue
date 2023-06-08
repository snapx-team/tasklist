<template>
    <div v-if="contractsData !== null">
        <div class="bg-gray-100 w-full h-64 absolute top-0 rounded-b-lg" style="z-index: -1"></div>

        <div class="flex flex-wrap p-4 pl-10">
            <h3 class="text-3xl text-gray-800 font-bold py-1 pr-8">Dashboard</h3>
        </div>

        <div class="mx-10 my-3 space-y-5 shadow-xl p-5 bg-white">

            <div>
                <button
                    v-if="!contractsListIsVisible"
                    @click="toggleContractsList"
                    class="py-4 font-semibold text-indigo-600 hover:text-indigo-800 transition duration-300 ease-in-out focus:outline-none">
                    Show Contracts
                    <i class="fa fa-th-large ml-2"></i>
                </button>
            </div>
            <div class="h-full flex" :key="componentKey">
                <div v-if="contractsListIsVisible"
                     class="bg-indigo-50 border-r-8 border-grey-100" style="width:30rem">
                    <ContractsPane :contracts-data="contractsData"/>
                </div>
                <splitpanes @resize="checkPaneSize()" class="default-theme" v-if="selectedContract">
                    <pane :size="50" min-size="30">
                        <PaneContent :selected-item="selectedContract" :type="'contract'"/>
                    </pane>
                    <pane :size="50" min-size="30" v-if="jobSitePaneIsVisible"
                          class="bg-indigo-50">
                        <PaneContent :selected-item="selectedJobSite" :type="'jobSite'"/>
                    </pane>
                </splitpanes>
                <div v-else class="w-full">
                    <div class="pl-4 flex flex-col justify-around items-center bg-white" style="height:803px;">
                        <h1 class="text-gray-300 text-5xl rounded-l gp-4 font-light pt-10 animate-pulse">select a
                            contract</h1>
                        <CityScapeBackground/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {axiosCalls} from "../../mixins/axiosCallsMixin";
import {Pane, Splitpanes} from "splitpanes";
import "splitpanes/dist/splitpanes.css";
import ContractsPane from "./dashboardComponents/ContractsList.vue";
import moment from "moment";
import Counter from "../global/Counter";
import PaneContent from "./dashboardComponents/PaneContent.vue";
import CityScapeBackground from "../global/CityScapeBackground.vue";

export default {

    inject: ["eventHub"],

    components: {
        CityScapeBackground,
        PaneContent,
        Counter,
        ContractsPane,
        Splitpanes,
        Pane
    },
    mixins: [axiosCalls],

    mounted() {
        this.getContractsData();
    },

    data() {
        return {
            contractsData: null,
            contractsListIsVisible: true,
            selectedContract: null,
            selectedJobSite: null,
            componentKey: 0,
            selectedDateRange: [moment().startOf('week').toDate(), new Date()],
            jobSitePaneIsVisible: false,
            shortcuts: [
                {text: 'Today', onClick: () => [new Date(), new Date()]},
                {
                    text: 'Yesterday',
                    onClick: () => [moment().subtract(1, 'day').toDate(), new Date()]
                },
                {
                    text: 'Start of Week',
                    onClick: () => [moment().startOf('week').toDate(), new Date()]
                },
                {
                    text: 'Start of Month',
                    onClick: () => [moment().startOf('month').toDate(), new Date()]
                },
                {
                    text: 'Last Week',
                    onClick: () => [moment().subtract(1, 'week').toDate(), new Date()]
                },
                {
                    text: 'Last Month',
                    onClick: () => [moment().subtract(1, 'month').toDate(), new Date()]
                }
            ],
        };
    },

    created() {
        this.eventHub.$on("set-selected-contract", (contract) => {
            this.setSelectedContract(contract);
        });
        this.eventHub.$on("set-selected-job-site", (contractJobSiteData) => {
            this.setSelectedJobSite(contractJobSiteData);
        });
        this.eventHub.$on("toggle-contracts-list", () => {
            this.toggleContractsList();
        });
        this.eventHub.$on("toggle-job-site-task-pane", () => {
            this.toggleJobSiteTasksPane();
        });
    },

    beforeDestroy() {
        this.eventHub.$off('set-selected-contract');
        this.eventHub.$off('set-selected-job-site');
        this.eventHub.$off('toggle-contracts-list');
        this.eventHub.$off('toggle-job-site-task-pane');
    },

    methods: {

        checkPaneSize() {
            this.eventHub.$emit("check-pane-size:contract");
            this.eventHub.$emit("check-pane-size:jobSite");
        },

        getContractsData() {
            this.eventHub.$emit("set-loading-state", true);
            this.asyncGetAllActiveContracts().then((data) => {
                this.contractsData = data.data;
                this.eventHub.$emit("set-loading-state", false);
            });
        },

        updateDateRange() {
            this.getContractsData();
            this.componentKey++
        },

        toggleContractsList() {
            this.contractsListIsVisible = !this.contractsListIsVisible
            this.eventHub.$emit("check-pane-size:contract");
            this.eventHub.$emit("check-pane-size:jobSite");
        },

        toggleJobSiteTasksPane() {
            this.jobSitePaneIsVisible = !this.jobSitePaneIsVisible
            this.eventHub.$emit("check-pane-size:contract");
            this.eventHub.$emit("check-pane-size:jobSite");
        },

        setSelectedContract(contract) {
            this.selectedContract = contract;
            this.selectedJobSite = null;
            this.jobSitePaneIsVisible = false;
            this.eventHub.$emit("reload-tasks:contract");
        },
        setSelectedJobSite(contractJobSiteData) {
            this.selectedJobSite = contractJobSiteData;
            this.jobSitePaneIsVisible = true;
            this.eventHub.$emit("reload-tasks:jobSite");

        },
    },
};
</script>

<style scoped>

.splitpanes__pane {
    height: auto;
}

.list-inline-item {
    cursor: pointer;
}

</style>
