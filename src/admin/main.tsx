import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './App';
import './styles/index.css';

// Declare global types for WordPress
declare global {
  interface Window {
    gstAdminData: {
      apiUrl: string;
      nonce: string;
      currentUser: any;
      adminUrl: string;
      themeUrl: string;
    };
  }
}

const container = document.getElementById('gst-admin-app');
if (container) {
  const root = createRoot(container);
  root.render(<App />);
}
