import { createApp } from 'vue'
import Article from './Article.vue'
import { FontAwesomeIcon, FontAwesomeLayers, FontAwesomeLayersText } from '@fortawesome/vue-fontawesome'
import { install as VueMonacoEditorPlugin } from '@guolao/vue-monaco-editor'
import "vue-search-select/dist/VueSearchSelect.css"
import 'vue-color-kit/dist/vue-color-kit.css'
import 'quill/dist/quill.snow.css'
let as_widgets = document.querySelectorAll('.as-article-widget-data');
as_widgets.forEach(as_widget => {
    let app = createApp(Article, {
        widget_json_id : as_widget.id
    });
    app.use(VueMonacoEditorPlugin, {
        paths: {
            // The default CDN config
            vs: 'https://cdn.jsdelivr.net/npm/monaco-editor@0.36.0/min/vs'
        },
    });
    app.component('font-awesome-icon', FontAwesomeIcon);
    app.component('font-awesome-layers', FontAwesomeLayers);
    app.component('font-awesome-layers-text', FontAwesomeLayersText);
    app.mount('#'+as_widget.id);
});