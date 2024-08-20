import { createApp } from 'vue'
import App from './App.vue'
const divroot = document.createElement("div");
divroot.setAttribute('id', 'as-free-templates');
document.body.appendChild(divroot);
createApp(App).mount('#as-free-templates');