import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
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
                brand: {
                    DEFAULT: '#0B5A9E',
                    50: '#f0f7ff',
                    100: '#e0effe',
                    200: '#bae0fd',
                    300: '#7cc5fb',
                    400: '#38a5f8',
                    500: '#0B5A9E',
                    600: '#094f8c',
                    700: '#084a82',
                    800: '#083a66',
                    900: '#0b3254',
                    950: '#072038',
                },
            },
            boxShadow: {
                'xs': '0 1px 2px 0 rgba(0, 0, 0, 0.04)',
            },
        },
    },

    plugins: [forms],
};
