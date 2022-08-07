import axios from "axios";

export default class Pagination {

    constructor(itemsName, URI, perPage = 20, params = []) {
        this.for = itemsName;
        this.endpoint = URI;
        this.rateLimit = 0;
        this.loading = false;

        this.page = 1;
        this.nextPage = 2;
        this.prevPage = 0;
        this.perPage = perPage
        this.data = [];
        this.response = null;
        this.opts = params;
    }
    
    getPage() {
        let endpoint = this.endpoint + '?page=' + this.page + '&perPage=' + this.perPage;
        if(this.opts.length > 0) {
            for(let i = 0; i < this.opts.length; i++) {
                endpoint += '&' + this.opts[i].name + '=' + this.opts[i].value;
            }
        }

        this.loading = true;
        this.data = axios.get(endpoint).then(response => {
            this.rateLimit = response.headers['x-ratelimit-remaining'];
            if(response.data[this.for] !== undefined){
                this.data = response.data[this.for];

                this.response = response.data;
            } else {
                this.data = response.data.data;

                this.response = response.data;
            }

            this.loading = false;
        })
    }

    setPage(page) {
        let pageInt = (typeof page === 'number') ? page : parseInt(page.target.value);

        if(pageInt > this.response.totalPages || pageInt > this.response.last_page || pageInt < 1) {
            return;
        }

        this.page = (typeof page == "number" ? page : page.target.value);
        this.nextPage = parseInt(this.page) + 1;
        this.prevPage = parseInt(this.page) - 1;
        this.getPage();
    }

    pageNext() {
        if(this.nextPage > this.response.totalPages || this.nextPage > this.response.last_page) {
            return;
        }

        this.page = this.nextPage;
        this.nextPage++;
        this.prevPage++;
        this.getPage();
    }

    setOption(key, value) {
        let exists = false;
        for(let i = 0; i < this.opts.length; i++) {
            if(this.opts[i].name == key) {
                exists = true;
                this.opts[i].value = value;
            }
        }

        if(!exists) {
            this.opts[key]['name'] = key;
            this.opts[key]['value'] = value;
        }
    }

    pagePrev() {
        if(this.prevPage == 0) {
            return;
        }

        this.page = this.prevPage;
        this.nextPage--;
        this.prevPage--;
        this.getPage();
    }
}