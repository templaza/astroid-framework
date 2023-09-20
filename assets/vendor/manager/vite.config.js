import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  build: {
    manifest: true,
    rollupOptions: {
      output: {
        manualChunks: false,
        inlineDynamicImports: true,
        entryFileNames: '[name].js',   // currently does not work for the legacy bundle
        assetFileNames: '[name].[ext]', // currently does not work for images
      }
    },
  }
})
