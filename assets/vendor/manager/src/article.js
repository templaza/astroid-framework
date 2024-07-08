import { createApp } from 'vue'
import Article from './Article.vue'
import './assets/article_data.scss'
let as_widgets = document.querySelectorAll('.as-article-widget-data');
as_widgets.forEach(as_widget => {
    createApp(Article, {
        widget_json_id : as_widget.id
    }).mount('#'+as_widget.id);
});