// https://vitepress.dev/guide/custom-theme
import {h} from 'vue'
import DefaultTheme from 'vitepress/theme'
import Cta from './components/Cta.vue'
import OptInOverlay from "./components/OptInOverlay.vue";
import Spacer from './components/Spacer.vue'
import OptInLink from './components/OptInLink.vue'
import Youtube from './components/Youtube.vue'
import AudioPlayer from './components/AudioPlayer.vue'
import './style.css'

/** @type {import('vitepress').Theme} */
export default {
    extends: DefaultTheme,
    Layout: () => {
        return h(DefaultTheme.Layout, null, {
            // https://vitepress.dev/guide/extending-default-theme#layout-slots
            'nav-bar-content-after': () => h(Cta),
            'layout-bottom': () => h(OptInOverlay),
        })
    },
    enhanceApp({app, router, siteData}) {
        app.component('OptInLink', OptInLink)
        app.component('Spacer', Spacer)
        app.component('Youtube', Youtube)
        app.component('AudioPlayer', AudioPlayer)
        if (typeof window !== 'undefined') {
            const script = document.createElement('script')
            script.src = 'https://f.convertkit.com/ckjs/ck.5.js'
            document.body.appendChild(script)
        }
    }
}
