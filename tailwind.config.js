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
                sans: ['"Manrope"', ...defaultTheme.fontFamily.sans],
                display: ['"Outfit"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                base: {
                    0: 'rgb(var(--base-0) / <alpha-value>)',
                    50: 'rgb(var(--base-50) / <alpha-value>)',
                    100: 'rgb(var(--base-100) / <alpha-value>)',
                    200: 'rgb(var(--base-200) / <alpha-value>)',
                    300: 'rgb(var(--base-300) / <alpha-value>)',
                    500: 'rgb(var(--base-500) / <alpha-value>)',
                    700: 'rgb(var(--base-700) / <alpha-value>)',
                    900: 'rgb(var(--base-900) / <alpha-value>)',
                    950: 'rgb(var(--base-950) / <alpha-value>)',
                },
                brand: {
                    50: 'rgb(var(--brand-50) / <alpha-value>)',
                    100: 'rgb(var(--brand-100) / <alpha-value>)',
                    200: 'rgb(var(--brand-200) / <alpha-value>)',
                    400: 'rgb(var(--brand-400) / <alpha-value>)',
                    600: 'rgb(var(--brand-600) / <alpha-value>)',
                    700: 'rgb(var(--brand-700) / <alpha-value>)',
                    800: 'rgb(var(--brand-800) / <alpha-value>)',
                    900: 'rgb(var(--brand-900) / <alpha-value>)',
                },
                accent: {
                    50: 'rgb(var(--accent-50) / <alpha-value>)',
                    100: 'rgb(var(--accent-100) / <alpha-value>)',
                    300: 'rgb(var(--accent-300) / <alpha-value>)',
                    500: 'rgb(var(--accent-500) / <alpha-value>)',
                    700: 'rgb(var(--accent-700) / <alpha-value>)',
                },
                status: {
                    success: 'rgb(var(--status-success) / <alpha-value>)',
                    warning: 'rgb(var(--status-warning) / <alpha-value>)',
                    danger: 'rgb(var(--status-danger) / <alpha-value>)',
                    info: 'rgb(var(--status-info) / <alpha-value>)',
                },
            },
            boxShadow: {
                soft: '0 20px 50px -32px rgba(15, 12, 8, 0.45)',
                glass: '0 22px 60px -36px rgba(10, 9, 7, 0.55)',
                inset: 'inset 0 1px 0 rgba(255, 255, 255, 0.18)',
            },
            borderRadius: {
                xl: '1.15rem',
                '2xl': '1.75rem',
                '3xl': '2rem',
            },
            opacity: {
                12: '0.12',
                15: '0.15',
                35: '0.35',
                85: '0.85',
                88: '0.88',
            },
        },
    },

    plugins: [forms],
};
