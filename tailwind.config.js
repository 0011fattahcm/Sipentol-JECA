/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./templates/**/*.php",
    "./src/**/*.php",
    "./webroot/js/**/*.js",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
  safelist: [
    "bg-gradient-to-br",
    "from-indigo-600",
    "to-indigo-500",
    "bg-white/10",
    "backdrop-blur-md",
    "text-indigo-100",
    "text-indigo-50",
  ],
};
