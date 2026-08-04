import { createApp } from 'vue';
import { createPinia } from 'pinia';
import '@fontsource-variable/inter/wght.css';
import '@fontsource-variable/newsreader/opsz.css';
import '@fontsource-variable/newsreader/opsz-italic.css';
import App from './App.vue';
import router from './router';
import { reveal } from './directives/reveal';
import './assets/main.css';

createApp(App).use(createPinia()).use(router).directive('reveal', reveal).mount('#app');
