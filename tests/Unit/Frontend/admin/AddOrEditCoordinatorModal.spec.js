/* eslint-disable no-undef*/

import {createLocalVue, shallowMount} from '@vue/test-utils';
import AddOrEditCoordinatorModal
    from '../../../../src/resources/js/components/admin/adminComponents/AddOrEditEmployeeModal.vue';
import Vue from 'vue';
import {eventNames} from '../../../../src/resources/js/enums/eventNames';

const localVue = createLocalVue();

describe('AddOrEditEmployeeModal.vue', () => {

    // testing createEmployeess event

    let wrapper;

    beforeEach(() => {
        wrapper = shallowMount(AddOrEditCoordinatorModal, {
            localVue,
            mocks: {
                $role: 'admin',
            },

            provide() {
                return {
                    eventHub: new Vue(),
                };
            },
        });
    });

    it('Checks that when an createEmployeess event is emitted without any option, it opens modals and sets default values', () => {

        expect(wrapper.vm.modalOpen).toBe(false);
        wrapper.vm.eventHub.$emit(eventNames.createEmployeess);
        expect(wrapper.vm.modalOpen).toBe(true);
        expect(wrapper.vm.tasklistData.role).toEqual('employee');
        expect(wrapper.vm.tasklistData.id).toEqual(null);
        expect(wrapper.vm.isEdit).toEqual(false);

    });

    it('Checks that when an createEmployeess event is emitted without any option, it opens modals and sets default values', () => {

        const tasklist = {
            role: 'test',
            id: 10,
            user: {id: 100}
        };

        expect(wrapper.vm.modalOpen).toBe(false);
        wrapper.vm.eventHub.$emit(eventNames.createEmployeess, tasklist);
        expect(wrapper.vm.modalOpen).toBe(true);
        expect(wrapper.vm.tasklistData.role).toEqual('test');
        expect(wrapper.vm.tasklistData.id).toEqual(10);
        expect(wrapper.vm.tasklistData.selectedUsers).toEqual([{id: 100}]);
        expect(wrapper.vm.tasklistData.user).toEqual({id: 100});
        expect(wrapper.vm.isEdit).toEqual(true);

    });

    // testing saveCoordinators function

    it('Checks that when you save a tasklist the modal closes', () => {

        wrapper.vm.tasklistData = {
            id: null,
            role: 'employee',
            selectedUsers: []
        };
        wrapper.vm.modalOpen = true;
        expect(wrapper.vm.modalOpen).toBe(true);
        wrapper.vm.saveCoordinators();
        expect(wrapper.vm.modalOpen).toBe(false);
    });

    it('Checks that when try to save a tasklist without selecting a user, a toast message is shown', () => {

        wrapper.vm.tasklistData = {
            id: null,
            role: 'employee',
        };

        wrapper.vm.triggerErrorToast = jest.fn();
        wrapper.vm.modalOpen = true;
        expect(wrapper.vm.modalOpen).toBe(true);
        wrapper.vm.saveCoordinators();
        expect(wrapper.vm.modalOpen).toBe(true);
        expect(wrapper.vm.triggerErrorToast).toHaveBeenCalled();
    });

    // testing deleteEmployees function

    it('Checks that when you delete a tasklist the modal closes', () => {

        wrapper.vm.modalOpen = true;
        expect(wrapper.vm.modalOpen).toBe(true);
        wrapper.vm.deleteEmployee();
        expect(wrapper.vm.modalOpen).toBe(false);
    });
});
