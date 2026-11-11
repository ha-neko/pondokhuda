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
        id: '/',
        name: 'Pondok Huda',
        short_name: 'Pondok Huda',
        description: 'Aplikasi penghuni kost Pondok Huda',
        lang: 'id',
        theme_color: '#2e7d32',
        background_color: '#f6faf3',
        display: 'standalone',
        orientation: 'portrait',
        start_url: '/',
        scope: '/',
        categories: ['lifestyle', 'finance'],
        shortcuts: [
          { name: 'Pembayaran', short_name: 'Bayar', url: '/bayar', icons: [{ src: 'icons/icon-192.png', sizes: '192x192' }] },
          { name: 'Keluhan', short_name: 'Keluhan', url: '/keluhan', icons: [{ src: 'icons/icon-192.png', sizes: '192x192' }] }
        ],
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
    // dev-only: allow Cloudflare/ngrok tunnel hostnames
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
