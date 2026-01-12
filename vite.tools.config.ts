import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  build: {
    lib: {
      entry: 'resources/js/tools/car-list/index.tsx',
      name: 'CarListWidget',
      fileName: () => 'car-list.js',
      formats: ['iife'], // <-- brug IIFE for at få alt ind i én fil
    },
    outDir: 'public/tools',
    emptyOutDir: false,
    rollupOptions: {
      // Tving React ind i bundle:
      external: [], // Ingen externals
      output: {
        globals: {}, // ikke nødvendigt når IIFE
      },
    },
  },
})
