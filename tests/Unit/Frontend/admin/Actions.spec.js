/* eslint-disable no-undef*/

import {createLocalVue, shallowMount} from '@vue/test-utils';
import Actions
    from '../../../../src/resources/js/components/admin/adminComponents/Actions';
import Vue from 'vue';
import {eventNames} from '../../../../src/resources/js/enums/eventNames';

const localVue = createLocalVue();

describe('AddOrEditEmployeeModal.vue', () => {

    // testing createEmployeess event

    let wrapper;

    beforeEach(() => {
        wrapper = shallowMount(Actions, {
            localVue,
            mocks: {
                $role: 'admin',
            },

            provide() {
                return {
                    eventHub: new Vue()
                };
            },
        });
    });

    it('Checks that fires createEmployeess event', () => {

        jest.spyOn(wrapper.vm.eventHub, '$emit');
        wrapper.vm.createEmployeess();
        expect(wrapper.vm.eventHub.$emit).toHaveBeenCalledTimes(1);
        expect(wrapper.vm.eventHub.$emit).toHaveBeenCalledWith(eventNames.createEmployeess);
    });
});
