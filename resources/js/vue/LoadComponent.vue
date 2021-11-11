<template>
  <div class="uk-load-entity"
       :class="loadDoneClass"
       v-html="loadEntity">
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      loadEntity: null,
      loadDoneClass: null
    }
  },
  props: [
    'entity',
    'options',
    'preload',
  ],
  created() {
    if (this.preload !== undefined && this.preload) {
      this.loadEntity = this.preload;
    }
  },
  updated() {
    if (typeof this.callbackCommands !== 'undefined' && this.callbackCommands !== null) {
      for (let $i = 0; $i < this.callbackCommands.length; ++$i) {
        let c = this.callbackCommands[$i]
        if (window['cmd_' + c.command] != undefined) window['cmd_' + c.command](c.options)
      }
    }
  },
  async mounted() {
    let r = {}
    let t = this
    if (t.options !== undefined && t.options) {
      let o = t.options.split(';')
      if (Array.isArray(o)) {
        o.forEach(function (item, i, o) {
          item = item.trim().split('=')
          if (item[0].trim()) r[item[0].trim()] = item[1] == undefined ? null : item[1].trim()
        });
      }
    }
    await axios.post(`/load`, {
      entity: t.entity,
      options: r
    })
        .then(xhr => {
          if (xhr.status == 200 && typeof xhr.data !== 'undefined') {
            if (typeof xhr.data.object !== 'undefined') {
              t.loadEntity = xhr.data.object
              t.loadDoneClass = 'uk-load-done'
            }
            if (typeof xhr.data.commands !== 'undefined') {
              if (xhr.data.commands !== null) {
                t.callbackCommands = xhr.data.commands
              }
            }
          }
        })
        .catch(error => {
          console.log('ErrorLoad:: ' + error)
        })
  }
}
</script>
