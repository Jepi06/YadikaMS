module.exports = {
  content: ['./resources/views/**/*.blade.php'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
        display: ['"Space Grotesk"', 'sans-serif'],
      },
      colors: {
        brand: {
          50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 500: '#3b82f6',
          600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a', 950: '#0f172a',
        },
        ocean: {
          950: '#061325', 900: '#0a1d37', 850: '#0d2547',
          800: '#0f2f5a', 700: '#15437f', 600: '#1d5aa6',
        },
      },
    },
  },
};