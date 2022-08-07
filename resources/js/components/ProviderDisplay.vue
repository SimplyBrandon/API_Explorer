<template>
    <div class="absolute top-0 right-0 bottom-0 left-0 bg-black bg-opacity-50 z-20" @click="this.closeProvider()"></div>
    <div class="absolute top-0 right-0 bottom-0 left-0 flex items-center justify-center z-30">
        <div :class="{ 'w-1/3': this.provider !== null }" class="flex flex-col bg-white rounded-lg shadow-lg p-8">
            <div v-if="this.provider == null" class="flex items-center">
                <i class="fa fa-spinner fa-spin text-lg mr-6"></i>
                <h2 class="font-semibold">Loading Provider</h2>
            </div>
            <div class="flex flex-col" v-else>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="mr-4 bg-blue-900 text-white rounded-md text-sm p-3 font-semibold">{{ this.provider.uuid }}</span>
                        <div class="flex flex-col">
                            <h2 class="font-semibold">{{ this.provider.name }}</h2>
                            <p class="text-gray-600">Last updated: {{ $filters.timeAgo(this.provider.updated_at) }} <i @click="this.forceUpdate()" class="fa fa-sync ml-2 cursor-pointer text-gray-600 hover:text-black text-xs"></i></p>
                        </div>
                    </div>
                    <i @click="this.closeProvider()" class="fa fa-times text-lg p-2 text-gray-600 hover:text-black transition ease duration-200 cursor-pointer"></i>
                </div>
                <div class="flex flex-col mt-6 rounded-md shadow-md overflow-hidden">
                    <iframe
                        style="border:0; width: 100%; height: 300px;"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"
                        :src="this.providerGoogleMapsURL()">
                        </iframe>
                </div>
                <div class="flex flex-col overflow-y-auto mt-6" style="max-height: 450px">
                    <div class="flex items-center justify-between border border-gray-200 rounded-md p-4 mb-4 last:mb-0 mr-3" v-for="(value, key) in this.providerData" :key="key">
                        <h3 class="font-semibold text-gray-700">{{ key.replace(/([A-Z])/g, ' $1').replace(/^./, function(str){ return str.toUpperCase(); }) }}</h3>
                        <p class="text-gray-700">{{ value }}</p>
                    </div>
                </div>
                <a :href="getAPILink()" target="_blank" class="flex flex-col w-full py-4 text-center rounded-md shadow-md mt-4 bg-blue-900 text-gray-300 hover:text-white transition ease duration-200">
                    <h2 class="font-semibold text-sm uppercase">View raw in the API</h2>
                    <p class="text-sm mt-2">(opens in a new tab)</p>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['providerId'],
    data() {
        return {
            provider: null,
        }
    },
    mounted() {
        axios.get('/api/cqc/providers/' + this.providerId)
        .then(response => {
            this.provider = response.data
        })
    },
    methods: {
        closeProvider() {
            console.log('closing...');
            this.$emit('unset');
        },
        providerGoogleMapsURL() {
            return 'https://www.google.com/maps/embed/v1/place?key=AIzaSyCuFYnBa-1L5VDhN6n5IwnJWhM5kvckDgk&q=' + this.provider.postcode;
        },
        getAPILink() {
            return '/api/cqc/providers/' + this.provider.uuid;
        },
        forceUpdate() {
            axios.get('/api/cqc/providers/' + this.provider.uuid + '?forceUpdate=true')
            .then(response => {
                this.provider = response.data
            })
        }
    },
    filters: {
        // create a filter to format the timestamp
        formatDate: function (value) {
            if (value) {
                return moment(String(value)).format('DD/MM/YYYY hh:mm')
            }
        }
    },
    computed: {
        providerData: function() {
            // remove keys from the provider.info object that we don't want to display
            let providerData = this.provider.info;
            
            delete providerData['providerId'];
            delete providerData['name'];
            delete providerData['locationIds'];
            delete providerData['contacts'];
            delete providerData['relationships'];
            delete providerData['regulatedActivities'];
            delete providerData['inspectionAreas'];
            delete providerData['inspectionCategories'];

            return providerData;
        }
    }
}
</script>
