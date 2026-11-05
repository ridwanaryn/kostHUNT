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
            colors: {
                canvas: '#F4F0EA',
                yellow: '#FFE600',
                cyan: '#00F0FF',
                pink: '#FF90E8',
                mint: '#38EF7D',
                coral: '#FF6B4A',
                lilac: '#C4B5FD',
                onyx: '#121212',
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Space Grotesk"', 'Syne', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                'hard-sm': '2px 2px 0px #000000',
                hard: '4px 4px 0px #000000',
                'hard-lg': '6px 6px 0px #000000',
                'hard-xl': '8px 8px 0px #000000',
            },
            borderWidth: {
                3: '3px',
            },
        },
    },

    plugins: [forms],
};
