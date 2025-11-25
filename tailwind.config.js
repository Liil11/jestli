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
                darkDeep: "#121212",
                darkest: "#0C0C0C",
                tealSecond: "#1F5E5E",
                tealFont: "#133A3A",
                grayComp: "#212121",
                formcolor: "#333333",
                formtext: "#969696",
                feedsbg: "#1c1c1c",
                grayShadow: "#3b3b3b",
            },
        },
    },

    plugins: [forms],
};
