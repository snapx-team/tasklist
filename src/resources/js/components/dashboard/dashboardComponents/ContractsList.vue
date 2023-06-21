<template>
    <div class="flex flex-wrap flex-col">
        <div class="flex justify-between p-2 bg-indigo-800 border-b">
            <div class="flex items-center">
                <button
                    class="bg-indigo-700 hover:bg-indigo-500 transition duration-150 ease-in-out rounded px-2 py-1 mr-2"
                    v-if="panelName === panelNames.jobSites"
                    @click="previousPanel">
                    <i class="fas fa-arrow-left text-white"></i>
                </button>
                <h1 class="text-white">{{ panelName }}</h1>
            </div>
            <div>
                <button
                    @click="togglePane"
                    class="focus:outline-none flex flex-col items-center text-gray-400 hover:text-gray-500 transition duration-150 ease-in-out pl-8"
                    type="button">
                    <i class="fas fa-times"></i>
                    <span
                        class="text-xs font-semibold text-center leading-3 uppercase p-1">Esc</span>
                </button>
            </div>
        </div>
        <div class="py-3 overflow-auto" style="height:750px;">
            <transition mode="out-in"
                        :name="this.transitionName">
                <div class="block"
                     v-if="panelName === panelNames.contracts"
                     key="1">
                    <contract-card
                        v-for="contract in contractsData"
                        :key="contract.id"
                        :contract="contract"
                        @click.native="selectContract(contract)"
                    ></contract-card>
                </div>
                <div class="block"
                     v-if="panelName === panelNames.jobSites">
                    <jobSiteCard
                        v-for="(addressData, index) in selectedContract.addresses"
                        :addressData=addressData
                        :key="index"
                        @click.native="selectJobSite(addressData)">
                        {{ addressData.address }}
                    </jobSiteCard>
                </div>
            </transition>
        </div>
    </div>
</template>
<script>
import JobSiteCard from "./JobSiteCard"
import ContractCard from "./ContractCard.vue"
import {axiosCalls} from "../../../mixins/axiosCallsMixin";

export default {
    inject: ["eventHub"],
    components: {JobSiteCard, ContractCard},
    props: {
        contractsData: {},
    },
    mixins: [axiosCalls],

    mounted() {
        this.panelName = this.panelNames.contracts;
        this.transitionName = this.transitionNames.next;
    },

    data() {
        return {
            selectedContract: {},
            transitionName: "",
            panelName: "",
            panelNames: {
                contracts: "Contracts",
                jobSites: "JobSites"
            },
            transitionNames: {
                next: "next",
                previous: "previous"
            },
        };
    },

    methods: {
        nextPanel() {
            this.transitionName = this.transitionNames.next;
            this.panelName = this.panelNames.jobSites;

        },
        previousPanel() {
            this.transitionName = this.transitionNames.previous;
            this.panelName = this.panelNames.contracts;
        },

        selectContract(contract) {
            if (contract.addresses.length > 0) {
                this.selectedContract = contract;
                this.eventHub.$emit("set-selected-contract", contract);
                this.nextPanel();
            } else {
                this.triggerInfoToast('error');
            }
        },
        selectJobSite(addressData) {
            const contractJobSiteData = {
                contractJobSite: addressData,
                contract: this.selectedContract
            }
            this.eventHub.$emit("set-selected-job-site", contractJobSiteData);
        },
        togglePane() {
            this.eventHub.$emit("toggle-contracts-list");
        },
    }
}
</script>

<style scoped>
.list-inline-item {
    cursor: pointer;
}

.next-leave, .previous-leave {
    opacity: 1;
}

.next-leave-active, .previous-leave-active {
    transition: all .2s ease
}

.next-leave-to {
    opacity: 0;
    transform: translateX(-50px);
}

.next-enter {
    opacity: 0;
    transform: translateX(50px);
}

.next-enter-active, .previous-enter-active {
    transition: all .2s ease
}

.next-enter-to, .previous-enter-to {
    opacity: 1;
}

.previous-leave-to {
    opacity: 0;
    transform: translateX(50px);
}

.previous-enter {
    opacity: 0;
    transform: translateX(-50px);
}
</style>
