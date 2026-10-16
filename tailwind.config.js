/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#F1F9EF',
                    100: '#DFF0DA',
                    200: '#BFE2B5',
                    300: '#95CE85',
                    400: '#63B44E',
                    500: '#287D1B',
                    600: '#216714',
                    700: '#1B5211',
                    800: '#163F0E',
                    900: '#11300B',
                    950: '#0B2108',
                },
                accent: {
                    50: '#FFF4EB',
                    100: '#FFE5D0',
                    200: '#FFC9A3',
                    300: '#FFA96B',
                    400: '#FC8A2E',
                    500: '#F26C00',
                    600: '#CC5A00',
                    700: '#A64A00',
                    800: '#803900',
                    900: '#5C2900',
                    950: '#3D1B00',
                },
                slate: {
                    50: '#F4F7F7',
                    100: '#E6EDEE',
                    200: '#CFDBDD',
                    300: '#AFC2C5',
                    400: '#83999E',
                    500: '#5F787E',
                    600: '#4B6167',
                    700: '#3D4F54',
                    800: '#2B3C40',
                    900: '#162E33',
                    950: '#0C1B1F',
                },
                surface: {
                    DEFAULT: '#FFFFFF',
                    muted: '#F6F7F9',
                    inverse: '#162E33',
                },
            },
            fontFamily: {
                sans: [
                    'Inter',
                    'system-ui',
                    '-apple-system',
                    'Segoe UI',
                    'Roboto',
                    'Helvetica Neue',
                    'Arial',
                    'sans-serif',
                ],
            },
            fontSize: {
                'fluid-hero': 'clamp(2.25rem, 1.2rem + 4.5vw, 4.5rem)',
                'fluid-h1': 'clamp(1.85rem, 1.1rem + 3vw, 3rem)',
                'fluid-h2': 'clamp(1.5rem, 1.05rem + 1.8vw, 2.25rem)',
            },
            boxShadow: {
                soft: '0 1px 2px 0 rgb(15 23 42 / 0.06), 0 6px 24px -8px rgb(15 23 42 / 0.14)',
                card: '0 4px 24px -8px rgb(15 23 42 / 0.25)',
                lifted: '0 2px 4px 0 rgb(15 23 42 / 0.08), 0 16px 40px -12px rgb(15 23 42 / 0.22)',
                sheet: '0 -8px 40px -12px rgb(15 23 42 / 0.25)',
            },
            borderRadius: {
                '4xl': '2rem',
            },
            minHeight: {
                touch: '44px',
            },
            minWidth: {
                touch: '44px',
            },
        },
    },
    plugins: [],
};