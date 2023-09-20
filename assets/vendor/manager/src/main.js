import { createApp } from 'vue'
import App from './App.vue'
import 'bootstrap/dist/css/bootstrap.css'
import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min'
import { FontAwesomeIcon, FontAwesomeLayers, FontAwesomeLayersText } from '@fortawesome/vue-fontawesome'
import { install as VueMonacoEditorPlugin } from '@guolao/vue-monaco-editor'
import './assets/base.scss'
window.bootstrap = bootstrap;
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