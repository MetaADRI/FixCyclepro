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
                    50: '#FDF0F1',
                    100: '#FBE0E2',
                    200: '#F6BFC4',
                    300: '#F09AA2',
                    400: '#E96874',
                    500: '#E33A45',
                    600: '#C92B36',
                    700: '#A5222B',
                    800: '#7E1B22',
                    900: '#5A1318',
                },
                accent: {
                    50: '#ECFDFA',
                    100: '#CFF9F3',
                    200: '#A1F1E7',
                    300: '#63E3D4',
                    400: '#26CCBB',
                    500: '#0EA5A4',
                    600: '#0A8384',
                    700: '#0B6A6B',
                    800: '#0D5455',
                    900: '#104647',
                },
                surface: {
                    DEFAULT: '#FFFFFF',
                    muted: '#F6F7F9',
                    inverse: '#0F172A',
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