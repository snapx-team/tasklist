<template>
    <li class="px-3 py-2 flex justify-between items-center bg-white cursor-pointer border-b border-l border-gray">
        <div class="flex items-center">
                <i class="fa fa-map-marker-alt mr-2 text-red-500"></i>
            <div class="ml-3 leading-4 text-gray-600 tracking-wide">
                <p class="font-semibold text-sm">{{ addressData.address }}</p>
            </div>
        </div>
    </li>
</template>
<script>

export default {

    props: {
        addressData: {
            type: Object,
            default: () => ({})
        }
    },
};
</script>
