/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#0A5FFF',
          dark: '#0844CC',
          light: '#2B7AFF',
        },
        secondary: {
          DEFAULT: '#00C897',
          dark: '#00A078',
          light: '#00E6B5',
        },
        dark: {
          base: '#0F172A',
          bg: '#111827',
          card: '#1F2937',
          border: '#374151',
        },
        text: {
          light: '#E5E7EB',
          muted: '#9CA3AF',
          dark: '#111827',
        },
      },
      fontFamily: {
        sans: ['Inter', 'Poppins', 'system-ui', 'sans-serif'],
        myanmar: ['Noto Sans Myanmar', 'Inter', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
