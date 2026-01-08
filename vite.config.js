import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig(({ command }) => {
  const isProduction = command === 'build';
  
  // WordPress URL - change this to match your actual WordPress site URL
  const wpUrl = process.env.WP_URL || 'http://localhost:8883';
  
  return {
    // Fixed: Use correct theme directory name
    base: isProduction ? '/wp-content/themes/Portfolio-Theme/' : '/',
    
    build: {
      manifest: true,
      outDir: 'dist',
      emptyOutDir: true,
      rollupOptions: {
        input: {
          // Main entry point - CSS is imported here for HMR
          main: resolve(__dirname, 'js/main.js'),
        },
        output: {
          entryFileNames: 'js/[name].js',
          chunkFileNames: 'js/[name]-[hash].js',
          assetFileNames: (assetInfo) => {
            if (assetInfo.name && assetInfo.name.endsWith('.css')) {
              return 'css/[name][extname]';
            }
            return 'assets/[name]-[hash][extname]';
          }
        }
      },
    },
    
    // Server configuration for hot reload
    server: {
      host: "0.0.0.0", // Allow external connections
      port: 3000,
      strictPort: true,
      cors: true,
      hmr: {
        // For HMR to work when WordPress is on a different port,
        // explicitly set the host and port where the HMR server is running
        host: 'localhost',
        port: 3000,
        protocol: 'ws',
        clientPort: 3000,
      },
      origin: wpUrl, // WordPress site URL for CORS
      watch: {
        include: ["css/**/*.css", "js/**/*.js"],
        usePolling: true,
      },
    },
    // Public directory
    publicDir: false,
    
    css: {
      devSourcemap: true,
    },
  };
});
