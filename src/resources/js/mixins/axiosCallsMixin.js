import axios from 'axios';

export const axiosCalls = {

    methods: {

        // App Data

        asyncGetAdminPageData() {
            return axios.get('get-admin-page-data').catch((error) => {
                this.loopAllErrorsAsTriggerErrorToast(error);
            });
        },

        // Tasks

        asyncCreateTask(taskData) {
            return axios.post('create-task', taskData).then(() => {
                this.triggerSuccessToast('Task Added');
            }).catch((error) => {
                this.loopAllErrorsAsTriggerErrorToast(error);
            });
        },

        asyncDeleteTask(taskId) {
            return axios.post('delete-task/' + taskId).then(() => {
                this.triggerSuccessToast('Task Removed');
            }).catch((error) => {
                this.loopAllErrorsAsTriggerErrorToast(error);
            });
        },


        asyncEditTask(taskId, description) {
            return axios.post('edit-task/' + taskId, {description}).then(() => {
                this.triggerSuccessToast('Task Edited');
            }).catch((error) => {
                this.loopAllErrorsAsTriggerErrorToast(error);
            });
        },

        asyncGetGlobalContractTasks(contractId) {
            console.log(contractId);
            return axios.get('get-global-contract-tasks/' + contractId).then((res) => {
                return res.data.data;
            }).catch((error) => {
                this.loopAllErrorsAsTriggerErrorToast(error);
            });
        },

        asyncGetExpiredGlobalContractTasks(contractId) {
            return axios.get('get-expired-global-contract-tasks/' + contractId).then((res) => {
                return res.data.data;
            }).catch((error) => {
                this.loopAllErrorsAsTriggerErrorToast(error);
            });
        },

        asyncGetJobSiteTasks(jobSiteAddressId) {
            return axios.get('get-job-site-tasks/' + jobSiteAddressId).then((res) => {
                return res.data.data;
            }).catch((error) => {
                this.loopAllErrorsAsTriggerErrorToast(error);
            });
        },

        asyncGetExpiredJobSiteTasks(jobSiteAddressId) {
            return axios.get('get-expired-job-site-tasks/' + jobSiteAddressId).then((res) => {
                return res.data.data;
            }).catch((error) => {
                this.loopAllErrorsAsTriggerErrorToast(error);
            });
        },

        // Contracts

        asyncGetAllActiveContracts() {
            return axios.get('get-all-active-contracts').catch((error) => {
                this.loopAllErrorsAsTriggerErrorToast(error);
            });
        },

        // Employees

        asyncGetEmployeeProfile() {
            return axios.get('get-employee-profile').catch((error) => {
                this.loopAllErrorsAsTriggerErrorToast(error);
            });
        },

        asyncGetAllUsers() {
            return axios.get('get-all-users').catch((error) => {
                this.loopAllErrorsAsTriggerErrorToast(error);
            });
        },

        asyncGetSomeUsers(searchTerm) {
            if (searchTerm === '') {
                return axios.get('get-all-users').catch((error) => {
                    this.loopAllErrorsAsTriggerErrorToast(error);
                });
            }
            return axios.get('get-some-users/' + searchTerm).catch((error) => {
                this.loopAllErrorsAsTriggerErrorToast(error);
            });
        },

        asyncGetEmployees() {
            return axios.get('get-employees').catch((error) => {
                this.loopAllErrorsAsTriggerErrorToast(error);
            });
        },

        asyncCreateEmployees(employeeData) {
            return axios.post('create-employees', employeeData).then(() => {
                this.triggerSuccessToast('Employee Added!');
            }).catch((error) => {
                this.loopAllErrorsAsTriggerErrorToast(error);
            });
        },

        asyncDeleteEmployee(employeeId) {
            return axios.post('delete-employee/' + employeeId).then(() => {
                this.triggerSuccessToast('Employee Removed');
            }).catch((error) => {
                this.loopAllErrorsAsTriggerErrorToast(error);
            });
        },

        //Triggers
        triggerSuccessToast(message) {
            this.$toast.success(message, {
                position: 'bottom-right',
                timeout: 5000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: false,
                closeButton: 'button',
                icon: true,
                rtl: false
            });
        },

        triggerErrorToast(message) {
            this.$toast.error(message, {
                position: 'bottom-right',
                timeout: 5000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: false,
                closeButton: 'button',
                icon: true,
                rtl: false
            });
        },

        triggerInfoToast(message) {
            this.$toast.info(message, {
                position: 'bottom-right',
                timeout: 5000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: false,
                closeButton: 'button',
                icon: true,
                rtl: false
            });
        },

        // Loop all errors

        loopAllErrorsAsTriggerErrorToast(errorResponse) {
            if ('response' in errorResponse && 'errors' in errorResponse.response.data) {
                let errors = [];
                Object.values(errorResponse.response.data.errors).map(error => {
                    errors = errors.concat(error);
                });
                errors.forEach(error => this.triggerErrorToast(error));
            } else if (errorResponse.response.data.message) {
                this.triggerErrorToast(errorResponse.response.data.message);
            }
        }
    }
};
