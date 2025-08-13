import './bootstrap'
import '@vueform/multiselect/themes/default.css'
import { createSSRApp, h, ref } from 'vue'
import { Link, createInertiaApp } from '@inertiajs/vue3'
import { createPinia } from 'pinia'
import VueLazyLoad from 'vue3-lazyload'
import Seo from '@/Components/Seo.vue'
import trans from '@/Composables/transComposable'
import Toast from "vue-toastification";
// Import the CSS or use your own!
import "vue-toastification/dist/index.css";
// Import persona mapping utility
import { registerPersonaDirective } from '@/Utils/personaMapping'
// Import route mapping utility
import { registerEnhancedRoute } from '@/Utils/routeMapping'
// Import terminology plugin
import terminologyPlugin from '@/Plugins/terminologyPlugin'
// Import global components registration
import { registerGlobalComponents } from '@/Components/index'

const appName = document.querySelector('meta[name="app-name"]')?.content || 'Laravel'

const language = ref(false)

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    const page = pages[`./Pages/${name}.vue`]
    if (page) {
      page.default.layout = page.default.layout ?? null
      return page
    } else {
      console.log('Invalid page', name)
    }
  },
  setup({ el, App, props, plugin }) {
    return createSSRApp({ render: () => h(App, props) })
      .mixin({ methods: { trans, route: window.route } })
      .component('Link', Link)
      .component('Seo', Seo)
      .use(plugin)
      .use(VueLazyLoad, {
        loading: '/assets/images/lazy.svg'
      })
      .use(createPinia())
      .use(Toast, {})
      // Register persona directive for automatic text replacement
      .use(app => registerPersonaDirective(app))
      // Register enhanced route function
      .use(app => registerEnhancedRoute(app))
      // Register terminology plugin for consistent term replacement
      .use(terminologyPlugin)
      // Register global components
      .use(app => registerGlobalComponents(app))
      .mount(el)
  },
  progress: {
    color: '#d2f34c',
    showSpinner: true
  }
})
