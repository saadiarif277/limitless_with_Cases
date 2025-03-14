const defaultTheme = require("tailwindcss/defaultTheme");
import colors from "tailwindcss/colors";
import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import("tailwindcss").Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Roboto", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: "#1363DF",
                    50: "#B1CDF8",
                    100: "#9EC1F7",
                    200: "#79A9F4",
                    300: "#5391F0",
                    400: "#2E79ED",
                    500: "#1363DF",
                    600: "#0F4CAB",
                    700: "#0A3578",
                    800: "#061E44",
                    900: "#010710",
                    950: "#000000"
                },
                secondary: {
                    DEFAULT: "#3F3D56",
                    50: "#C7C6D6",
                    100: "#BBBACE",
                    200: "#A4A2BD",
                    300: "#8D8AAC",
                    400: "#76729B",
                    500: "#625F86",
                    600: "#504E6E",
                    700: "#3F3D56",
                    800: "#272635",
                    900: "#0F0E14",
                    950: "#030304",
                },
                gray: colors.slate,
            },
        },
    },

    corePlugins: {
        backdropOpacity: false,
        backgroundOpacity: false,
        borderOpacity: false,
        divideOpacity: false,
        ringOpacity: false,
        textOpacity: false
    },

    plugins: [
        require("@tailwindcss/forms"),
    ],
};
