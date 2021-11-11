window._ = require('lodash');
window.Vue = require('vue');
window.axios = require('axios');

window.axios.defaults.headers.common = {
    'X-CSRF-TOKEN': window.Laravel.csrfToken,
    'X-Requested-With': 'XMLHttpRequest',
    'LOCALE': window.Laravel.locale,
    'LOCATION': '',
    'DEVICE': window.Laravel.device
};

// import loadComponent from './vue/LoadComponent.vue';
// import catalogFilterItemComponent from './vue/CatalogFilterItemList.vue';
// import catalogFilterItemSelected from './vue/CatalogFilterSelected.vue';

let app = new Vue({
    el: '#app',
    data: {
        catalogFilter: window.catalogFilterParam,
        catalogFilterSelected: window.catalogSelected,
        locale: window.Laravel.locale,
        refreshFilter: false
    },
    components: {
        loadComponent: () => import('./vue/LoadComponent'),
        catalogFilterComponent: () => import('./vue/CatalogFilter'),
        // catalogFilterItemComponent: () => import('./vue/CatalogFilterItemList'),
        // catalogFilterSelectedComponent: () => import('./vue/CatalogFilterSelected'),
    },
    watch: {
        refreshFilter: function (val, oldVal) {
            setTimeout(() => {
                this.refreshFilter = false;
            }, 500)
        }
    }
});
