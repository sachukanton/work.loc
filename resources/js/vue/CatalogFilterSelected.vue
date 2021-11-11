<template>
    <div class="filter-selected-box"
         v-if="visibleSelected">
        <div class="filter-selected-title">
            {{ translate[$root.locale].title }}
        </div>
        <div class="filter-selected-items">
            <div class="inside">
                <div v-for="(param, i) in links"
                     class="param uk-margin">
                    <div class="title">
                        {{ param.param.title }}
                    </div>
                    <div class="items"
                         v-if="param.param.type == 'select'">
                        <a v-for="(item, t) in param.options"
                           :href="item.alias"
                           data-hide_load="1"
                           class="item use-ajax">
                            <i>
                                <svg version="1.1"
                                     xmlns="http://www.w3.org/2000/svg"
                                     width="768"
                                     height="768"
                                     viewBox="0 0 768 768">
                                    <path d="M768 77.317l-306.683 306.683 306.683 306.683-77.317 77.317-306.683-306.683-306.683 306.683-77.317-77.317 306.683-306.683-306.683-306.683 77.318-77.317 306.683 306.683 306.683-306.683z"></path>
                                </svg>
                            </i>
                            {{ item.title }}
                        </a>
                    </div>
                    <div class="items"
                         v-if="param.param.type == 'range'">
                        <a :href="param.options[2].path + (param.options[2].query ? '?' + param.options[2].query : null)"
                           data-hide_load="1"
                           class="item use-ajax">
                            <i>
                                <svg version="1.1"
                                     xmlns="http://www.w3.org/2000/svg"
                                     width="768"
                                     height="768"
                                     viewBox="0 0 768 768">
                                    <path d="M768 77.317l-306.683 306.683 306.683 306.683-77.317 77.317-306.683-306.683-306.683 306.683-77.317-77.317 306.683-306.683-306.683-306.683 77.318-77.317 306.683 306.683 306.683-306.683z"></path>
                                </svg>
                            </i>
                            {{ param.options[0] }} - {{ param.options[1] }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="filter-selected-reset-link">
            <a :href="resetLink"
               class="reset-link use-ajax">
                {{ translate[$root.locale].resetLink }}
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                links: {},
                translate: {},
                resetLink: null,
                visibleSelected: false
            }
        },
        created() {
            this.links = catalogSelected;
            this.translate = {
                ru: {
                    title: 'Выбранные фильтры',
                    resetLink: 'Сбросить фильтр'
                },
                ua: {
                    title: 'Вибрані фільтри',
                    resetLink: 'Скинути фільтр'
                },
                en: {
                    title: 'Selected Filters',
                    resetLink: 'Reset Filter'
                }
            };
            this.resetLink = catalogCatalogUrl;
            if (this.links.length) this.visibleSelected = true;
        },
        props: [
            'refresh'
        ],
        mounted() {
        },
        computed: {},
        methods: {},
        watch: {
            refresh: function (val, oldVal) {
                if (val == true) {
                    this.links = catalogSelected;
                    this.visibleSelected = this.links.length ? true : false;
                }
            }
        }
    }
</script>