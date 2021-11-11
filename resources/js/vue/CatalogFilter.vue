<template>
  <div class="filter-catalog">
    <div class="uk-flex uk-flex-middle uk-margin-small-bottom"
         v-if="params.mark.length || params.mark.exception">
      <div class="mark-filter"
           v-if="params.mark.length">
        <div v-for="(item, i) in params.mark"
             :class="[(item.active ? 'uk-active' : null), (item.count_request == 0 ? 'no-result' : null)]"
             :key="i"
             class="uk-display-inline-block">
          <a v-bind:href="getAliasItem(item)"
             :rel="item.count_request == 0 && !item.active ? 'nofollow' : false"
             :class="['uk-button-color-' + item.id, (item.count_request != 0 || item.active ? 'use-ajax' : 'uk-disabled'), (item.active ? 'uk-active' : null)]"
             @click="load = true"
             v-html="item.style.icon"
             data-hideLoad="1"
             class="uk-button uk-button-link uk-position-relative">
          </a>
          <div class="mark-color-1 uk-drop uk-drop-top-center"
               :class="'mark-color-' + item.id"
               uk-drop="pos: top-center; delay-hide:0;">
            <div class="mark-color">
              <b> {{ item.title }} </b>
            </div>
          </div>
        </div>
      </div>
      <div class="uk-margin-left"
           v-if="params.exception.length">
        <a v-for="(item, i) in params.exception"
           :rel="item.count_request == 0 && !item.active ? 'nofollow' : false"
           :class="[(item.active ? 'uk-active' : null), (item.count_request != 0 || item.active ? 'use-ajax' : 'uk-disabled')]"
           :href="getAliasItem(item)"
           :key="i"
           @click="load = true"
           data-hideLoad="1"
           class="item-filter uk-display-inline-block uk-position-relative">
          {{ item.title }}
        </a>
      </div>
    </div>
    <div class="uk-flex-inline uk-flex-top main-filtration"
         v-if="params.others.length">
      <button class="btn-filter-params"
              type="button"
              @click="open = !open">
        <img data-src="/template/images/icon-filtration.svg"
             width="36"
             height="30"
             alt="Filter button" uk-svg>
      </button>
      <div class="filter-params-box uk-flex-1 uk-flex"
           v-if="open">

        <div class="filter-selected-reset-link uk-display-inline-block uk-position-relative">
          <a :href="resetLink"
             v-for="(item, index) in params.others.slice(0, 1)" :class=" [(item.active ? '' : 'uk-active')]"
             class="uk-display-inline-block reset-link use-ajax"
             >
            {{ translate[$root.locale].resetLink }}
          </a>
        </div>
        <transition-group name="list-others"
                          tag="div"
                          class="uk-flex">
          <a v-for="(item, i) in params.others"
             :rel="item.count_request == 0 && !item.active ? 'nofollow' : false"
             :class="[(item.active ? 'uk-active' : null), (item.count_request != 0 || item.active ? 'use-ajax' : 'uk-disabled')]"
             :href="getAliasItem(item)"
             :key="i"
             @click="load = true"
             data-hideLoad="1"
             class="item-filter uk-display-inline-block uk-position-relative">
            {{ item.title }}
          </a>
        </transition-group>
      </div>
    </div>
    <div class="loader"
         v-if="load">
      <div class="loader-inside"></div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      params: {},
      collapseFilter: false,
      load: false,
      open: false,
    }
  },
  created() {
    this.params = catalogFilterParam;
      this.translate = {
          ru: {
              resetLink: 'Все'
          },
          ua: {
              resetLink: 'Всі'
          },
      };
      this.resetLink = catalogCatalogUrl;
  },
  props: [
    'refresh'
  ],
  mounted() {
    this.$nextTick(() => {
    })
  },
  computed: {
    selectedList() {
      let t = this
      return t.chooseActiveOptions()
    },
  },
  methods: {
    getAliasItem(i) {
      let alias = i.alias
      if (!i.count_request) alias = 'javascript:void(0);'
      if (i.active) alias = i.alias_rollback
      return alias
    },
    chooseActiveOptions() {
      let t = this
      return t.params.others.filter(option => {
        return option.active
      });
    }
  },
  watch: {
    refresh: function (val, oldVal) {
      let t = this
      if (val == true) {
        t.params = catalogFilterParam
        t.chooseActiveOptions()
        t.load = false
      }
    }
  }
}
</script>

<style>
.loader {
  width: 100%;
  height: 3px;
  position: relative;
  z-index: 500;
  border-radius: 3px;
  -webkit-webkit-border-radius: 3px;
  -moz-webkit-border-radius: 3px;
  -ms-webkit-border-radius: 3px;
  -o-webkit-border-radius: 3px;
  webkit-border-radius: 3px;
  overflow: hidden;
}

.loader-inside {
  height: 100%;
  width: 100%;
  background: rgba(249, 249, 249, .75);
}

.loader-inside:before {
  height: 100%;
  width: 0;
  background: linear-gradient(to top, #ff4a00, #ff8f00);
  content: '';
  display: block;
  animation: getWidth 2s ease-in infinite;
}

.list-others-enter-active {
  animation: scaleIn .7s;
}

.list-others-leave-active {
  animation: scaleIn .5s reverse;
}

@keyframes getWidth {
  100% {
    width: 100%;
  }
}

@keyframes scaleIn {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.5);
  }
  100% {
    transform: scale(1);
  }
}
</style>
