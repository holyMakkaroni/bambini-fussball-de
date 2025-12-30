/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './components/**/*.{js,vue,ts}',
    './layouts/**/*.vue',
    './pages/**/*.vue',
    './storyblok/**/*.vue',
    './plugins/**/*.{js,ts}',
    './app.vue',
    './error.vue'
  ],
  corePlugins: {
    container: false
  },
  theme: {
    extend: {
      margin: {
        15: '3.75rem'
      },
      padding: {
        15: '3.75rem'
      },
      screens: {
        xxs: '340px',
        xs: '420px',
        xl: '1300px',
        'md-lg': '960px',
        '3xl': '1920px'
      },
      fontSize: {
        xxs: '10px'
      },
      fontFamily: {
        sans: ['Hind', 'sans-serif']
      },
      colors: {
        primary: {
          DEFAULT: '#FC4D0F',
          dark: '#ec460b'
        },
        secondary: {
          light: '#F4F4F4',
          DEFAULT: '#24272C'
        },
        success: '#6FAA0D',
        warning: '#FFCF00',
        info: '#0D76AA'
      }
    }
  },
  plugins: []
}
