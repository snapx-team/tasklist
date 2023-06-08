/* eslint-disable no-undef*/

import {createLocalVue, shallowMount} from '@vue/test-utils';
import UserCard from '../../../../src/resources/js/components/dashboard/dashboardComponents/ContractCard.vue';
import {supervisorMockData} from '../../../JsonMockData/supervisorMockData';

const localVue = createLocalVue();

describe('ContractCard.vue', () => {

    let wrapper;

    it('retrieve isActive if any supervisor shift has active shift', () => {
        let props = {supervisor: supervisorMockData()};

        wrapper = shallowMount(UserCard, {
            localVue,
            propsData: props,
        });
        expect(wrapper.vm.isActive).toBe(true);
    });

    it('assert isActive false if supervisor shift does not have active shift', () => {
        let props = {supervisor: supervisorMockData(false)};

        wrapper = shallowMount(UserCard, {
            localVue,
            propsData: props,
        });
        expect(wrapper.vm.isActive).toBe(false);
    });
});
