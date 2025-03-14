import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import RegisterComponents from "./register-components";

import RegisterPlugins from "./register-plugins";

// Vuetify imports

import { createVuetify } from 'vuetify';
import { VDialog, VCard, VFileInput, VBtn, VSpacer, VCardActions, VCardTitle, VCardText } from 'vuetify/components';
import { aliases, mdi } from 'vuetify/iconsets/mdi'; // For Material Design Icons

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Create Vuetify instance
const vuetify = createVuetify({
    components: {
        VDialog,
        VCard,
        VCardTitle,
        VCardText,
        VCardActions,
        VFileInput,
        VBtn,
        VSpacer,
    },

});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(RegisterComponents)
            .use(RegisterPlugins)
            .use(vuetify) // Add Vuetify to your app
            .mount(el);

        return app;
    },

});
