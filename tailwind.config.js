import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                darkGray: "#242424",
                tealPrimary: "#309898",
                darkDeep: "#1B1B1B",
                darkest: "#0C0C0C",
                tealSecond: "#1F5E5E",
                tealFont: "#133A3A",
                grayComp: "#303030",
                formcolor: "#434343",
            },
        },
    },

    plugins: [forms],
};
