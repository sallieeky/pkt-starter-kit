import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.jsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito Sans', ...defaultTheme.fontFamily.sans],
            },
            colors:{
                'white':"#ffffff",
                'primary' : {
                    DEFAULT:'#1268b3',
                    'main':'#1268b3',
                    'surface':'#f7fafc',
                    'border':'#c7ddf0',
                    'hover':'#0a4f86',
                    'pressed':'#0f416a',
                    'focus':'#edf5fc',
                    'disabled':'#c7ddf0',
                },
                'secondary' : {
                    DEFAULT:'#f47920',
                    'main':'#f47920',
                    'surface':'#fcf4ed',
                    'border':'#ffe4d1',
                    'hover':'#d36428',
                    'pressed':'#be5a27',
                    'focus':'#fcefe5',
                    'disabled':'#ffe4d1',
                },
                'success' : {
                    DEFAULT:'#7aa659',
                },
                'danger' : {
                    DEFAULT:'#cb3a31',
                    'main':'#cb3a31',
                    'surface':'#fff5f5',
                    'border':'#f5cbc9',
                    'hover':'#a32821',
                    'pressed':'#80251e',
                    'focus':'#f5dedc',
                    'disabled':'#f5cbc9',
                },
                'gray' : {
                    200:'#f6f6f6',
                    300:'#efeff0',
                    400:'#e4e4e4',
                    500:'#c9c9cb',
                    600:'#a9a9ac',
                    700:'#8a8a8d',
                    800:'#6c6c70',
                    900:'#4f4f54',
                }
            }
        },
    },
    safelist: [
        {
            pattern: /w-(20|64)/
        },
        {
            pattern: /pl-(20|64)/
        },
    ],
    plugins: [forms],
};
