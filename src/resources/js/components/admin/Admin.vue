<template>
    <div v-if="adminPageData !== null">
        <div class="bg-gray-100 w-full h-64 absolute top-0 rounded-b-lg" style="z-index: -1"></div>

        <div class="flex flex-wrap p-4 pl-10">
            <h3 class="text-3xl text-gray-800 font-bold py-1 pr-8">Admin</h3>
        </div>

        <div class="mx-10 my-3 space-y-5 shadow-xl p-5 bg-white">
            <actions :employeesLength="adminPageData.employees.length"></actions>

            <employee-list :class="{ 'animate-pulse': loadingEmployees }"
                           :employees="adminPageData.employees"></employee-list>

            <add-or-edit-employee-modal></add-or-edit-employee-modal>
        </div>
    </div>
</template>

<script>
import EmployeeList from "./adminComponents/EmployeeList.vue";
import Actions from "./adminComponents/Actions.vue";
import AddOrEditEmployeeModal from "./adminComponents/AddOrEditEmployeeModal.vue";
import {axiosCalls} from "../../mixins/axiosCallsMixin";

export default {

    inject: ["eventHub"],

    components: {
        AddOrEditEmployeeModal,
        EmployeeList,
        Actions,
    },

    mixins: [axiosCalls],

    mounted() {
        this.getAdminPageData();
    },

    data() {
        return {
            filter: "",
            adminPageData: null,
            loadingEmployees: false,
        };
    },

    created() {
        this.eventHub.$on("save-employees", (employeeData) => {
            this.saveEmployees(employeeData);
        });

        this.eventHub.$on("delete-employee", (tasklistId) => {
            this.deleteEmployee(tasklistId);
        });
    },

    beforeDestroy() {
        this.eventHub.$off('save-employees');
        this.eventHub.$off('delete-employee');
    },

    methods: {
        saveEmployees(employeeData) {
            this.loadingEmployees = true;
            const cloneEmployeeData = {...employeeData};
            this.asyncCreateEmployees(cloneEmployeeData).then(res => {
                this.asyncGetEmployees().then((data) => {
                    this.adminPageData.employees = data.data;
                    this.loadingEmployees = false;
                });
            });
        },

        deleteEmployee(tasklistId) {
            this.loadingEmployees = true;
            this.asyncDeleteEmployee(tasklistId).then(res => {
                this.asyncGetEmployees().then((data) => {
                    this.adminPageData.employees = data.data;
                    this.loadingEmployees = false;
                });
            });
        },

        getAdminPageData() {
            this.eventHub.$emit("set-loading-state", true);
            this.asyncGetAdminPageData().then((data) => {
                this.adminPageData = data.data;
                this.eventHub.$emit("set-loading-state", false);
            });
        },
    },
};
</script>


