/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        primary:   '#0f172a',
        secondary: '#1e293b',
        accent:    '#b45309',
        'accent-light': '#fef3c7',
      },
      fontFamily: {
        serif: ['"Playfair Display"', 'serif'],
        sans:  ['"Plus Jakarta Sans"', 'sans-serif'],
      },
    },
  },
  plugins: [],
}