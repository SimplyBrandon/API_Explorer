<template>
    <div class="flex flex-col h-full flex-grow">
        <div class="flex flex-col flex-grow overflow-y-auto">
            <div class="flex flex-col bg-gray-900 p-6 pb-32 md:p-20 md:pb-40">
                <div class="flex items-center">
                    <i class="fa fa-archive text-4xl text-white"></i>
                    <div class="flex flex-col ml-4">
                        <h2 class="font-semibold text-white">Care Quality Commission</h2>
                        <p class="text-sm font-light text-gray-300">Providers API Explorer</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col -mt-20 md:px-20">
                <div class="flex flex-col bg-white md:rounded-md shadow-md w-full md:p-10">
                    <div class="flex flex-col p-4 bg-blue-900 text-white shadow-md md:rounded-md text-sm w-full md:w-1/3">
                        <p><i class="fa fa-circle-o"></i> <i class="fa fa-chevron-right mx-2"></i> <i class="fa fa-circle mr-4"></i> Select a provider to begin multi/mass functions.</p>
                        <p><i class="fa fa-mouse mr-4 mt-4"></i> Click a provider's name to preview filtered information about the provider.</p>
                        <p><i class="fa fa-filter mr-4 mt-4"></i> Ordering by columns is only available on locally stored providers.</p>
                    </div>
                    <table class="relative border-collapse md:rounded-md overflow-hidden shadow-md mt-4" v-if="this.pagination.data">
                        <thead class="text-white">
                            <tr>
                                <th class="p-4 bg-blue-900 border-r-2 border-blue-800"></th>
                                <th @click="this.setOrderBy('name')" class="px-4 py-6 cursor-pointer bg-blue-900 border-r-2 border-blue-800" style="width: 420px">
                                    <div class="flex items-center">
                                        <i v-if="this.orderBy !== null" :class="{ 'fa-chevron-right': this.orderBy !== 'name', 'fa-chevron-down': this.orderBy == 'name', 'fa-chevron-up': this.orderBy == 'name_desc'}" class="fa mr-3" style="width: 30px"></i>
                                        <h2 class="font-semibold text-sm">Name</h2>
                                    </div>
                                </th>
                                <th @click="this.setOrderBy('type')" class="px-4 py-6 cursor-pointer bg-blue-900 border-r-2 border-blue-800 hidden md:table-cell">
                                    <div class="flex items-center">
                                        <i v-if="this.orderBy !== null" :class="{ 'fa-chevron-right': this.orderBy !== 'type', 'fa-chevron-down': this.orderBy == 'type', 'fa-chevron-up': this.orderBy == 'type_desc' }" class="fa mr-3" style="width: 30px"></i>
                                        <h2 class="font-semibold text-sm">Type</h2>
                                    </div>
                                </th>
                                <th @click="this.setOrderBy('postcode')" class="px-4 py-6 cursor-pointer bg-blue-900 border-r-2 border-blue-800 hidden md:table-cell">
                                    <div class="flex items-center">
                                        <i v-if="this.orderBy !== null" :class="{ 'fa-chevron-right': this.orderBy !== 'postcode', 'fa-chevron-down': this.orderBy == 'postcode', 'fa-chevron-up': this.orderBy == 'postcode_desc' }" class="fa mr-3" style="width: 30px"></i>
                                        <h2 class="font-semibold text-sm">Postcode</h2>
                                    </div>
                                </th>
                                <th class="px-4 py-6 cursor-pointer bg-blue-900 border-r-2 border-blue-800 hidden md:table-cell">
                                    <div class="flex items-center">
                                        <i v-if="this.orderBy !== null" class="fa fa-chevron-right mr-3 opacity-0" style="width: 30px"></i>
                                        <h2 class="font-semibold text-sm">Status</h2>
                                    </div>
                                </th>
                                <th class="p-4 bg-blue-900"></th>
                            </tr>
                        </thead>
                        <tbody v-if="this.pagination.data.length > 0">
                            <tr v-for="provider in this.pagination.data" :key="provider.providerId" :class="{ 'opacity-60 odd:bg-gray-100 even:bg-gray-50': !provider.local && this.selected.includes(provider.providerId) == false, 'bg-white opacity-100': this.selected.includes(provider.providerId) == true, 'hover:bg-white text-gray-600 hover:text-black odd:bg-gray-100 even:bg-gray-50': this.selected.includes(provider.providerId) == false && provider.local }" class="cursor-pointer border-b-2 border-gray-200 last:border-b-0 transition ease duration-200" :ref="'provider-' + provider.providerId">
                                <td @click="toggleSelected(provider.providerId)" class="p-4 border-r-2 border-gray-200">
                                    <div class="flex items-center justify-center w-full">
                                        <i v-if="this.selected.includes(provider.providerId) == true" class="fa fa-circle"></i>
                                        <i v-if="this.selected.includes(provider.providerId) == false" class="fa fa-circle-o"></i>
                                    </div>
                                </td>
                                <td class="p-4 border-r-2 border-gray-200" @click="this.viewProvider(provider.providerId)">
                                    <div class="flex items-center">
                                        <h2 class="font-semibold text-sm">{{ provider.providerName }}</h2>
                                    </div>
                                </td>
                                <td class="p-4 border-r-2 border-gray-200 hidden md:table-cell">
                                    <div class="flex items-center">
                                        <h2 class="font-semibold text-sm">{{ (provider.local !== null ? provider.local.info.type : "Unknown") }}</h2>
                                    </div>
                                </td>
                                <td class="p-4 border-r-2 border-gray-200 hidden md:table-cell">
                                    <div class="flex items-center">
                                        <h2 class="font-semibold text-sm">{{ (provider.local !== null ? provider.local.info.postalCode : "Unknown") }}</h2>
                                    </div>
                                </td>
                                <td class="p-4 border-r-2 border-gray-200 hidden md:table-cell">
                                    <div v-if="provider.local !== null" class="flex items-center">
                                        <h2 class="font-semibold text-sm" v-if="provider.local.info.registrationStatus == 'Registered'">
                                            {{ provider.local.info.registrationStatus }} - {{ provider.local.info.registrationDate }}
                                        </h2>
                                        <h2 class="font-semibold text-sm" v-if="provider.local.info.registrationStatus == 'Deregistered'">
                                            {{ provider.local.info.registrationStatus }} - {{ provider.local.info.registrationDate }} - {{ provider.local.info.deregistrationDate }}
                                        </h2>
                                    </div>
                                    <div v-else>
                                        <h2 class="font-semibold text-sm">
                                            Unknown Status
                                        </h2>
                                    </div>
                                </td>
                                <td class="flex items-center justify-center p-4 border-r-2 border-gray-200 bg-white" @click="saveProvider(provider.providerId)">
                                    <div class="flex flex-col items-center" v-if="provider.local !== null">
                                        <div class="flex items-center font-bold text-sm">
                                            <i :class="{ 'fa-sync bg-white text-gray-600': provider.local.is_outdated == false, 'fa-exclamation-triangle text-white bg-red-900': provider.local.is_outdated == true }" class="fa p-3 rounded-md mr-4 cursor-pointer shadow-md"></i>
                                            <h2>Update</h2>
                                        </div>
                                    </div>
                                    <h2 class="font-bold text-sm" v-if="provider.local == null"><i class="fa fa-save p-3 rounded-md mr-4 cursor-pointer bg-white shadow-md text-gray-600 hover:text-black"></i> Retrieve</h2>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-if="this.pagination.data.length == 0 && this.loading">
                            <tr>
                                <td colspan="6" class="p-20">
                                    <div class="flex flex-col items-center justify-center w-full">
                                        <i class="fa fa-spinner fa-spin text-5xl"></i>
                                        <h2 class="font-semibold text-sm mt-4">Loading Providers</h2>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-if="this.pagination.data.length == 0 && !this.loading">
                            <tr>
                                <td colspan="6" class="p-20">
                                    <div class="flex flex-col items-center justify-center w-full">
                                        <i class="fa fa-exclamation-triangle text-5xl"></i>
                                        <h2 class="font-semibold text-sm mt-4">No Providers Found</h2>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="absolute flex flex-col bottom-0 right-0 md:mr-10 md:mb-10 w-full md:w-1/4 shadow-lg rounded-md overflow-hidden">
        <div class="flex flex-col bg-white p-5">
            <h2 class="font-semibold text-sm">You have selected {{ this.selected.length }} provider(s)</h2>
            <div class="flex items-center justify-between mt-3">
                <div class="flex flex-col">
                    <div class="flex items-center text-xs">
                        <button @click="this.selectAll()" type="button" class="bg-blue-900 text-gray-300 hover:text-white font-semibold text-sm px-4 py-3 rounded-md mr-4">Select All</button>
                        <button v-if="this.selected.length > 0" @click="this.deselectAll()" type="button" class="bg-blue-900 text-gray-300 hover:text-white font-semibold text-sm px-4 py-3 rounded-md">Deselect All</button>
                    </div>
                </div>
                <button v-if="this.savingSelected == false && this.selected.length > 0" type="button" @click="this.saveSelected" class="bg-blue-900 text-gray-300 hover:text-white font-semibold text-sm px-4 py-3 rounded-md">Update / Retrieve Selected</button>
                <h2 class="px-4 py-3 bg-blue-900 text-white font-bold text-sm rounded-md" v-if="this.savingSelected">{{ this.savingRemaining + " / " + this.selected.length }}</h2>
            </div>
        </div>
        <div class="flex items-center bg-blue-900">
            <button @click="this.pagination.pagePrev()" type="button" class="text-left w-1/3 p-4 text-gray-300 hover:text-white transition ease duration-200"><i class="fa fa-chevron-left mr-2"></i> Previous</button>
            <div class="flex flex-col w-1/3">
                <input type="text" name="pageNumber" class="text-center p-4 outline-none bg-transparent text-white border-l-2 border-r-2 appearance-none border-blue-800" :value="this.pagination.page" @blue="event => this.pagination.setPage(event)" @keyup.enter="event => this.pagination.setPage(event)">
            </div>
            <button @click="this.pagination.pageNext()" type="button" class="text-right w-1/3 p-4 text-gray-300 hover:text-white transition ease duration-200">Next <i class="fa fa-chevron-right ml-2"></i></button>
        </div>
        <div class="flex flex-col p-4 bg-gray-100">
            <div class="flex items-center">
                <label for="stored-only" class="inline-flex relative items-center cursor-pointer mr-6">
                    <input type="checkbox" value="" id="stored-only" class="sr-only peer">
                    <div @click="this.toggleLocalOnly()" class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-bold text-gray-900">Stored Only</span>
                </label>
            </div>
            <div class="flex items-center justify-between mb-2 mt-4">
                <label for="default-range" class="text-sm font-bold text-gray-900">Results shown per request</label>
                <p class="text-sm font-bold text-gray-900">{{ this.pagination.perPage }}</p>
            </div>
            <input id="default-range" type="range" :value="this.pagination.perPage" @change="event => { this.pagination.perPage = event.target.value; this.pagination.getPage() }" min="10" max="500" step="10" class="w-full h-2 bg-blue-900 rounded-lg appearance-none cursor-pointer">
            <div class="flex items-center justify-between mt-4 text-sm font-bold">
                <h2>Rate Limit Remaining</h2>
                <h2>{{ this.pagination.rateLimit + " / 60" }} | ~{{ this.secondsUntilRateLimitResets }}s until reset</h2>
            </div>
        </div>            
    </div>
    <provider-display v-if="this.viewingProvider !== null" :providerId="this.viewingProvider" @unset="this.unsetProviderInView()"></provider-display>
</template>

<script>
    import Pagination from '../scripts/pagination.js';
    import ProviderDisplay from '../components/ProviderDisplay.vue';

    export default {
        name: 'Home',
        components: {
            ProviderDisplay
        },
        data() {
            return {
                pagination: [],
                orderBy: null,
                localOnly: false,
                selected: [],
                savingSelected: false,
                savingRemaining: 0,
                selectingViewOption: true,
                secondsUntilRateLimitResets: 60,

                viewingProvider: null,
            }
        },
        mounted() {
            this.pagination = new Pagination('providers', '/api/cqc/providers', 30, [
                {
                    name: 'localOnly',
                    value: false
                },
                {
                    name: 'orderBy',
                    value: null
                }
            ]);

            setInterval(() => {
                if (this.secondsUntilRateLimitResets > 0) {
                    this.secondsUntilRateLimitResets--;
                } else {
                    this.secondsUntilRateLimitResets = 60;
                }
            }, 1000);

            this.pagination.getPage();
        },
        methods: {
            setOrderBy(orderBy) {
                if(this.orderBy == null){
                    return;
                }

                if (this.orderBy == orderBy) {
                    this.orderBy = orderBy + '_desc';
                } else {
                    this.orderBy = orderBy;
                }

                this.pagination.setOption('orderBy', this.orderBy);
                this.pagination.getPage();
            },
            toggleLocalOnly() {
                this.pagination.opts.map((key, index) => {
                    if (key.name == 'localOnly') {
                        this.pagination.opts[index].value = !this.pagination.opts[index].value;
                    }
                });

                let localOnlyVal = this.pagination.opts.find(opt => opt.name == 'localOnly').value;
                if(localOnlyVal == true){
                    this.localOnly = true;
                    this.orderBy = 'name';
                    this.pagination.setOption('orderBy', 'name');
                } else {
                    this.localOnly = false;
                    this.orderBy = null;
                    this.pagination.setOption('orderBy', null   );
                }

                this.pagination.perPage = 30;
                this.pagination.setPage(1);
            },
            saveProvider(providerId){
                axios.get('/api/cqc/providers/' + providerId).then((response) => {
                    this.pagination.rateLimit = response.headers['x-ratelimit-remaining'];
                    this.pagination.getPage();
                });
            },
            toggleSelected(providerId){
                if(this.savingSelected){
                    return;
                }
                if(this.selected.includes(providerId)){
                    this.selected.splice(this.selected.indexOf(providerId), 1);
                } else {
                    this.selected.push(providerId);
                }
            },
            selectAll(){
                this.pagination.data.map((provider) => {
                    if(!this.selected.includes(provider.providerId)){
                        this.selected.push(provider.providerId);
                    }
                });
            },
            deselectAll(){
                this.selected = [];
            },
            saveSelected(){
                this.savingSelected = true;
                this.savingRemaining = this.selected.length;

                this.selected.forEach(providerId => {
                    this.pagination.rateLimit--;
                    this.saveSelectedProvider(providerId).then(() => {
                        this.savingRemaining--;
                        if(this.savingRemaining == 0){
                            this.selected = [];
                            this.pagination.getPage();
                            this.savingSelected = false;
                        }
                    });
                })
            },
            saveSelectedProvider(providerId){
                return axios.get('/api/cqc/providers/' + providerId);
            },
            viewProvider(providerId){
                this.viewingProvider = providerId;
            },
            unsetProviderInView(){
                this.viewingProvider = null;
            }
        }
    }
</script>