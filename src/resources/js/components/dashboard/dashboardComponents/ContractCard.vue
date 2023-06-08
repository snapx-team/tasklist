<template>
    <li class="px-3 py-2 flex justify-between items-center bg-white cursor-pointer border-b border-l border-gray">
        <div class="flex items-center">
            <div class="relative">
                <avatar :name="contract.name" :size=8 :borderSize=1></avatar>
            </div>
            <div class="ml-3 leading-4 text-gray-700 tracking-wide">
                <p class="font-semibold text-sm">{{ contract.name }}</p>
                <small class="text-gray-400 text-xs">contract ID: {{ contract.id }}</small>
            </div>
        </div>
        <div class="flex items-center font-semibold">
            <h2 v-if="contract.addresses.length > 1">({{ contract.addresses.length }})</h2>
            <i class="fa fa-caret-right ml-2"></i>
        </div>
    </li>
</template>
<script>

import Avatar from "../../global/Avatar";

export default {

    components: {
        Avatar
    },

    props: {
        contract: {
            type: Object,
            default: () => ({})

        }
    },
};
</script>
