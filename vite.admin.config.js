import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
  plugins: [react()],
  root: './src/admin',
  build: {
    outDir: '../../dist/admin',
    emptyOutDir: true,
    rollupOptions: {
      input: './src/admin/main.tsx',
      output: {
        entryFileNames: 'index.js',
        chunkFileNames: '[name].js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'index.css';
          }
          return '[name].[ext]';
        },
      },
      external: [
        'react',
        'react-dom',
        'wp',
      ],
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
      '@/lib': path.resolve(__dirname, './src/lib'),
      '@/components': path.resolve(__dirname, './src/components'),
      '@/ui': path.resolve(__dirname, './src/components/ui'),
    },
  },
  define: {
    global: 'globalThis',
  },
  css: {
    postcss: './postcss.config.js',
  },
  server: {
    watch: {
      usePolling: true,
    },
  },
});
