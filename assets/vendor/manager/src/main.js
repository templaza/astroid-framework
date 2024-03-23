import { createApp } from 'vue'
import App from './App.vue'
import 'bootstrap/scss/bootstrap.scss'
import "vue-search-select/dist/VueSearchSelect.css"
import 'vue-color-kit/dist/vue-color-kit.css'
import { Tooltip, Toast, Modal } from 'bootstrap/js/index.esm'
import { FontAwesomeIcon, FontAwesomeLayers, FontAwesomeLayersText } from '@fortawesome/vue-fontawesome'
import { install as VueMonacoEditorPlugin } from '@guolao/vue-monaco-editor'
import './assets/base.scss'
window.Tooltip = Tooltip;
window.Toast = Toast;
window.Modal = Modal;
const app = createApp(App);
app.use(VueMonacoEditorPlugin, {
    paths: {
        // The default CDN config
        vs: 'https://cdn.jsdelivr.net/npm/monaco-editor@0.36.0/min/vs'
    },
});
app.component('font-awesome-icon', FontAwesomeIcon);
app.component('font-awesome-layers', FontAwesomeLayers);
app.component('font-awesome-layers-text', FontAwesomeLayersText);
app.mount('#astroid-app')