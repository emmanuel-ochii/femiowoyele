import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js,ts}'],
  theme: {
    extend: {
      colors: {
        navy: '#0B1C32',
        forest: '#114B3A',
        sand: '#F4EFE7',
        warmgrey: '#C0B6A4',
        gold: '#C9A45C',
        ink: '#182235',
        mist: '#F8F7F4',
      },
      fontFamily: {
        serif: ['Georgia', 'Cormorant Garamond', 'serif'],
        sans: ['Inter', 'Source Sans 3', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        soft: '0 18px 60px rgba(11, 28, 50, 0.08)',
      },
      keyframes: {
        floatIn: {
          '0%': { opacity: '0', transform: 'translateY(10px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
      },
      animation: {
        floatIn: 'floatIn 260ms ease-out both',
      },
    },
  },
  plugins: [typography],
};
