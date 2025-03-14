/**
 * Register Plugins.
 * 
 * Place and enable global plugins into this file.
 */

import ToastPlugin from "@/plugins/toast/index.js";

const registerPlugins = (app) => {
    app.use(ToastPlugin);
};

export default registerPlugins;