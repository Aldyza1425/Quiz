/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        "primary": "#13a4ec",
        "primary-hover": "#0e8bc7",
        "secondary": "#0d9488",
        "background-light": "#f6f7f8",
        "background-dark": "#101c22",
        "surface-light": "#ffffff",
        "surface-dark": "#1c2e36",
        "border-light": "#e5e7eb",
        "border-dark": "#2d3748",
      },
      fontFamily: {
        "sans": ["Lexend", "sans-serif"],
        "display": ["Lexend", "sans-serif"]
      },
      borderRadius: {
        "DEFAULT": "0.25rem",
        "lg": "0.5rem",
        "xl": "0.75rem",
        "full": "9999px"
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
