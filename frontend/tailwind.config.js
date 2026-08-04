import typography from '@tailwindcss/typography';

/**
 * Design tokens for FemiOwoyele.com.
 *
 * The palette follows the brand brief (deep navy, forest green, warm sand,
 * muted gold) but expands each brand colour into a full tonal scale so that
 * borders, surfaces, and text can share one family instead of relying on
 * ad-hoc opacity values.
 */
/**
 * Tailwind's default opacity scale has gaps (no 12, 45, 72, 92 …), and a colour
 * modifier that misses the scale is silently dropped rather than erroring. The
 * design uses fine-grained alpha for hairlines and scrims, so the scale is
 * widened to every whole percent.
 */
const opacity = Object.fromEntries(Array.from({ length: 101 }, (_, i) => [i, String(i / 100)]));

export default {
  content: ['./index.html', './src/**/*.{vue,js,ts}'],
  theme: {
    extend: {
      opacity,
      colors: {
        navy: {
          DEFAULT: '#0B1C32',
          50: '#F3F7FB',
          100: '#E4ECF4',
          200: '#C7D6E6',
          300: '#9CB5D2',
          400: '#6B90BA',
          500: '#3E6A9E',
          600: '#2A507F',
          700: '#1B3A63',
          800: '#12294A',
          900: '#0B1C32',
          950: '#06101D',
        },
        forest: {
          DEFAULT: '#114B3A',
          50: '#F2F9F6',
          100: '#E1F1EA',
          200: '#C0E2D3',
          300: '#93CCB4',
          400: '#5FAF90',
          500: '#35946F',
          600: '#217A59',
          700: '#186048',
          800: '#114B3A',
          900: '#0C3529',
          950: '#06231A',
        },
        gold: {
          DEFAULT: '#C9A45C',
          50: '#FBF8F1',
          100: '#F6EFE0',
          200: '#EDE0C4',
          300: '#E1CBA0',
          400: '#D5B67C',
          500: '#C9A45C',
          600: '#AE873F',
          700: '#8C6A31',
          800: '#684E26',
          900: '#45341A',
        },
        sand: {
          DEFAULT: '#F4EFE7',
          50: '#FBF9F5',
          100: '#F4EFE7',
          200: '#EBE3D6',
          300: '#DCD1BD',
          400: '#C0B6A4',
          500: '#9E9280',
        },
        ink: {
          DEFAULT: '#182235',
          muted: '#4B5768',
          faint: '#6F7A8B',
          inverse: '#F6F8FB',
        },
        // Legacy aliases kept so older markup keeps rendering during the migration.
        warmgrey: '#C0B6A4',
        mist: '#F8F7F4',
      },
      fontFamily: {
        serif: ['"Newsreader Variable"', 'Newsreader', 'Georgia', 'Cambria', 'serif'],
        sans: ['"Inter Variable"', 'Inter', 'system-ui', '-apple-system', 'sans-serif'],
      },
      fontSize: {
        // Fluid editorial scale. Every step pairs a size with its natural leading.
        'display-1': ['clamp(2.85rem, 1.35rem + 5.6vw, 5.75rem)', { lineHeight: '1.02', letterSpacing: '-0.025em' }],
        'display-2': ['clamp(2.25rem, 1.45rem + 3.1vw, 4rem)', { lineHeight: '1.06', letterSpacing: '-0.022em' }],
        'display-3': ['clamp(1.875rem, 1.35rem + 2.1vw, 3rem)', { lineHeight: '1.12', letterSpacing: '-0.018em' }],
        'display-4': ['clamp(1.375rem, 1.15rem + 1vw, 1.9rem)', { lineHeight: '1.2', letterSpacing: '-0.012em' }],
        lead: ['clamp(1.0625rem, 1rem + 0.35vw, 1.3125rem)', { lineHeight: '1.68' }],
        micro: ['0.6875rem', { lineHeight: '1.4', letterSpacing: '0.14em' }],
      },
      maxWidth: {
        shell: '82rem',
        prose: '38rem',
        measure: '44rem',
      },
      spacing: {
        header: 'var(--header-height)',
      },
      borderRadius: {
        sm: '2px',
        DEFAULT: '3px',
      },
      boxShadow: {
        soft: '0 18px 60px -20px rgba(11, 28, 50, 0.18)',
        lift: '0 28px 70px -28px rgba(11, 28, 50, 0.32)',
        frame: '0 40px 90px -40px rgba(11, 28, 50, 0.45)',
      },
      transitionTimingFunction: {
        editorial: 'cubic-bezier(0.22, 0.61, 0.36, 1)',
      },
      keyframes: {
        floatIn: {
          '0%': { opacity: '0', transform: 'translateY(12px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        shimmer: {
          '100%': { transform: 'translateX(100%)' },
        },
      },
      animation: {
        floatIn: 'floatIn 420ms cubic-bezier(0.22, 0.61, 0.36, 1) both',
        shimmer: 'shimmer 1.6s infinite',
      },
    },
  },
  plugins: [typography],
};
