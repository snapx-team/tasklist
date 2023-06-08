/* eslint-disable no-undef*/

import {createLocalVue, shallowMount} from '@vue/test-utils';
import MapPane from '../../../../src/resources/js/components/dashboard/dashboardComponents/PaneContent.vue';
import Vue from 'vue';

const localVue = createLocalVue();

describe('Dashboard.vue', () => {

    let wrapper;

    beforeEach(() => {
        wrapper = shallowMount(MapPane, {
            localVue,
            mocks: {
                $role: 'admin',
            },

            propsData: {
                mapPaneData: {
                    jobSiteMarkers: [],
                    googleMapRefreshKey: 0,
                    options: {
                        zoom: 11,
                        center: {
                            lat: 45.533550,
                            lng: -73.602119,
                        },
                    },
                    infoPosition: null,
                    infoOptions: {
                        pixelOffset: {
                            width: 0,
                            height: -35,
                        },
                        maxWidth: 200,
                        content: null,
                    },
                    window_open: false,
                    currentMarkerIndex: null,
                    pathData: {
                        isLoadingPath: false,
                        stops: [],
                        stopsMarkers: [],
                        path: [],
                        totalDistance: 0,
                    },
                },
            },

            provide() {
                return {
                    eventHub: new Vue()
                };
            },
        });
    });

    it('openWindow google map function sets correct data', () => {

        let marker = {
            'label': {
                'text': '3',
                'color': '#fff',
                'fontWeight': 'bold',
                'fontSize': '16px'
            },
            'position': {
                'lat': 39.9713524,
                'lng': -75.159036
            },
            'name': 'Location 3',
            'address': 'Test Address'
        };

        const infoPosition = {
            'lat': 39.9713524,
            'lng': -75.159036
        };

        expect(wrapper.vm.mapPaneData.currentMarkerIndex).toBe(null);
        expect(wrapper.vm.mapPaneData.infoPosition).toBe(null);
        wrapper.vm.openWindow(marker, 1);
        expect(wrapper.vm.mapPaneData.currentMarkerIndex).toBe(1);
        expect(wrapper.vm.mapPaneData.infoPosition).toMatchObject(infoPosition);
    });
});
