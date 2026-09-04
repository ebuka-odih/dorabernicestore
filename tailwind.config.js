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
                sans: ['Jost', ...defaultTheme.fontFamily.sans],
                serif: ['"Cormorant Garamond"', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                ink: {
                    50: '#F6F5F3',
                    100: '#EBE8E3',
                    200: '#D3CDC3',
                    300: '#AFA595',
                    400: '#837663',
                    500: '#5E5245',
                    600: '#463C32',
                    700: '#362E27',
                    800: '#28221D',
                    900: '#1B1713',
                },
                gold: {
                    50: '#FBF8F3',
                    100: '#F5EEE2',
                    200: '#EADFC9',
                    300: '#DBC7A0',
                    400: '#C7A76C',
                    500: '#B08D4E',
                    600: '#93713B',
                    700: '#785A30',
                    800: '#5F462A',
                    900: '#4A3722',
                },
                cream: {
                    DEFAULT: '#FAF6F0',
                    dark: '#F1E9DD',
                },
                rose: {
                    500: '#A8695C',
                    600: '#8F5548',
                },
            },
            letterSpacing: {
                widest2: '0.25em',
            },
        },
    },

    plugins: [forms],
};
