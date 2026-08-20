import type { CapacitorConfig } from '@capacitor/cli'

const config: CapacitorConfig = {
  appId: 'id.pondokhuda.app',
  appName: 'Pondok Huda',
  webDir: 'dist',
  backgroundColor: '#0e1514',
  android: {
    allowMixedContent: true,
  },
  server: {
    // androidWebView options; keep default (capacitor://localhost) unless
    // a live URL is needed:
    // androidScheme: 'https',
  },
}

export default config
