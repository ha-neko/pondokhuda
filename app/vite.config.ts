import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'
import { VitePWA } from 'vite-plugin-pwa'

export default defineConfig({
  plugins: [
    react(),
    tailwindcss(),
    VitePWA({
      registerType: 'autoUpdate',
      includeAssets: ['favicon.png', 'brand-logo-white.png', 'brand-logo-black.png'],
      manifest: {
        name: 'PondokHuda',
        short_name: 'PondokHuda',
        description: 'Aplikasi penghuni kost PondokHuda',
        lang: 'id',
        theme_color: '#00696d',
        background_color: '#f4fbf9',
        display: 'standalone',
        orientation: 'portrait',
        start_url: '/',
        icons: [
          { src: 'icons/icon-192.png', sizes: '192x192', type: 'image/png' },
          { src: 'icons/icon-512.png', sizes: '512x512', type: 'image/png' },
          { src: 'icons/icon-maskable-512.png', sizes: '512x512', type: 'image/png', purpose: 'maskable' }
        ]
      },
      workbox: {
        globPatterns: ['**/*.{js,css,html,svg,png,woff2}'],
        navigateFallbackDenylist: [/^\/api/]
      },
      devOptions: { enabled: true }
    })
  ],
  server: {
    host: '0.0.0.0',
    // dev-only: allow tunnel hostnames (e.g. *.trycloudflare.com)
    allowedHosts: true,
    proxy: {
      '/api': { target: 'http://127.0.0.1:8081', changeOrigin: true }
    }
  },
  preview: {
    proxy: {
      '/api': { target: 'http://127.0.0.1:8081', changeOrigin: true }
    }
  }
})
