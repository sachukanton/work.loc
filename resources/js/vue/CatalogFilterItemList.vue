<template>
    <div class="filter-param-box open">
        <div class="filter-param-title">
            {{ param.title }}
        </div>
        <div class="filter-param-search uk-form-controls 123"
             v-if="param.type == 'select'">
            <input type="text"
                   v-model="search"
                   class="uk-input"
                   :placeholder="translate[$root.locale].searchInput" />
        </div>
        <div class="filter-param-items"
             v-if="param.type == 'select'"
             :class="[(isScroll ? 'scrollbar' : null)]">
            <div class="inside">
                <div v-for="(item, i) in filteredList"
                     :class="[(item.active ? 'active' : null), (item.count_request == 0 ? 'no-result' : null)]">
                    <a v-bind:href="getAliasItem(item)"
                       data-hide_load="1"
                       :rel="item.count_request == 0 ? 'nofollow' : false"
                       :class="item.count_request != 0 ? 'use-ajax' : 'uk-disabled uk-text-color-grey'">
                        {{ item.title }}
                    </a>
                    <span class="badge"
                          v-if="!item.active">
                        {{ item.count_request }}
                    </span>
                </div>
                <div v-if="filteredList.length == 0"
                     class="error">
                    {{ translate[$root.locale].noResults }}
                </div>
            </div>
        </div>
        <div class="filter-param-slider-range"
             v-if="param.type == 'range'">
            <div class="slider-range"></div>
            <div class="label-min">
                {{ options.min.default }}
            </div>
            <div class="label-max">
                {{ options.max.default }}
            </div>
        </div>
    </div>
</template>

<script>
    let timeOutRangeAjax = false;

    export default {
        data() {
            return {
                search: '',
                param: {},
                options: {},
                translate: {},
                open: true,
                collapseFilter: false,
                collapseParam: false,
                ranges: []
            }
        },
        created() {
            this.param = catalogFilterParam[this.param_id].param;
            this.options = catalogFilterParam[this.param_id].options;
            this.translate = {
                ru: {
                    searchInput: 'Поиск в списке',
                    noResults: 'Ничего не найдено',
                },
                ua: {
                    searchInput: 'Пошук в списку',
                    noResults: 'Нічого не знайдено',
                },
                en: {
                    searchInput: 'Search in List',
                    noResults: 'Nothing found',
                }
            };
            if (this.param.type == 'select' && Object.keys(this.options).length > 5) this.collapseFilter = true;
            if (this.param.type == 'select' && this.param.collapse) this.collapseParam = true;
        },
        props: [
            'param_id',
            'refresh'
        ],
        mounted() {
            this.$nextTick(() => {
                if (this.param.type == 'range') {
                    let v = this;
                    let el = this.$el;
                    let rangeInp = $(el).find('.slider-range');
                    let rangeVal = {
                        min: v.options.min.request,
                        max: v.options.max.request
                    }
                    v.ranges[v.param.name] = rangeInp.slider({
                        range: true,
                        min: v.options.min.default,
                        max: v.options.max.default,
                        values: [
                            v.options.min.request,
                            v.options.max.request
                        ],
                        create: function (event, ui) {
                            let handle = $(this).find('.ui-slider-handle');
                            let classHandle = v.options.min.request != v.options.min.default || v.options.max.request != v.options.max.default ? 'view' : '';
                            $(handle[0]).addClass('first').html('<i class="' + classHandle + '">' + v.options.min.request + '</i>');
                            $(handle[1]).addClass('last').html('<i class="' + classHandle + '">' + v.options.max.request + '</i>');
                        },
                        slide: function (event, ui) {
                            let handle = $(this).find('.ui-slider-handle');
                            let classHandle = ui.values[0] != v.options.min.default || ui.values[1] != v.options.max.default ? 'view' : '';
                            let alias = v.options.alias;
                            $(handle[0]).html('<i class="' + classHandle + '">' + ui.values[0] + '</i>');
                            $(handle[1]).html('<i class="' + classHandle + '">' + ui.values[1] + '</i>');
                            rangeVal.min = ui.values[0];
                            rangeVal.max = ui.values[1];
                        },
                        stop: function (event, ui) {
                            let alias = v.options.alias;
                            let selectedVal = rangeVal;
                            clearTimeout(timeOutRangeAjax);
                            timeOutRangeAjax = setTimeout(function () {
                                if (selectedVal.min != v.options.min.request || selectedVal.max != v.options.max.request) {
                                    let r = alias.path;
                                    let q = alias.query;
                                    if (q && (selectedVal.min != v.options.min.default || selectedVal.max != v.options.max.default)) {
                                        r += '?' + q + '&price[min]=' + selectedVal.min + '&price[max]=' + selectedVal.max;
                                    } else if (selectedVal.min != v.options.min.default || selectedVal.max != v.options.max.default) {
                                        r += '?price[min]=' + selectedVal.min + '&price[max]=' + selectedVal.max;
                                    } else if (q) {
                                        r += '?' + q;
                                    }
                                    rangeVal.changed = false;
                                    _ajax_post(rangeInp.parents('.filter-param-slider-range'), r, {}, false);
                                }
                            }, 2000);
                        }
                    });
                }
            })
        },
        computed: {
            filteredList() {
                let options = Object.values(this.options);
                let quantityList = options.length;
                return options.filter(option => {
                    let title = option.title.toString();
                    return title.toLowerCase().includes(this.search.toLowerCase())
                });
            },
            isScroll() {
                return Object.keys(this.options).length > 8 ? true : false;
            }
        },
        methods: {
            getAliasItem(i) {
                let alias = i.alias;
                if (i.active) alias = i.alias_rollback;
                if (!i.count_request) alias = 'javascript:void(0);';
                return alias;
            },
            openFilter() {
                if (this.open) {
                    this.open = false;
                } else {
                    this.open = true;
                }
            },
            forceRerenderRange() {
                let ranges = this.ranges;
                this.$nextTick(() => {
                    let v = this;
                    let el = this.$el;
                    if (v.param.type == 'range') {
                        if (ranges[v.param.name] != undefined) {
                            ranges[v.param.name].slider("values", [v.options.min.request, v.options.max.request])
                            let handle = $(el).find('.ui-slider-handle');
                            let classHandle = v.options.min.request != v.options.min.default || v.options.max.request != v.options.max.default ? 'view' : '';
                            let alias = v.options.alias;
                            $(handle[0]).html('<i class="' + classHandle + '">' + v.options.min.request + '</i>');
                            $(handle[1]).html('<i class="' + classHandle + '">' + v.options.max.request + '</i>');
                        }
                    }
                });
            }
        },
        watch: {
            refresh: function (val, oldVal) {
                if (val == true) {
                    this.param = catalogFilterParam[this.param_id].param;
                    this.options = catalogFilterParam[this.param_id].options;
                    this.forceRerenderRange();
                }
            }
        }
    }
</script>