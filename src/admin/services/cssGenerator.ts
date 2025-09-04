import { ThemeSettings } from './api';

export const generateCSSVariables = (settings: ThemeSettings): string => {
  return `
    :root {
      /* Colors */
      --gst-primary: ${settings.colors.primary};
      --gst-secondary: ${settings.colors.secondary};
      --gst-accent: ${settings.colors.accent};
      --gst-background: ${settings.colors.background};
      --gst-foreground: ${settings.colors.foreground};
      --gst-card: ${settings.colors.card};
      --gst-card-foreground: ${settings.colors.cardForeground};
      --gst-popover: ${settings.colors.popover};
      --gst-popover-foreground: ${settings.colors.popoverForeground};
      --gst-muted: ${settings.colors.muted};
      --gst-muted-foreground: ${settings.colors.mutedForeground};
      --gst-border: ${settings.colors.border};
      --gst-input: ${settings.colors.input};
      --gst-ring: ${settings.colors.ring};
      --gst-destructive: ${settings.colors.destructive};
      --gst-destructive-foreground: ${settings.colors.destructiveForeground};
      
      /* Typography */
      --gst-font-body: ${settings.typography.bodyFontFamily};
      --gst-font-heading: ${settings.typography.headingFontFamily};
      --gst-text-5xl: ${settings.typography.h1Size};
      --gst-text-4xl: ${settings.typography.h2Size};
      --gst-text-3xl: ${settings.typography.h3Size};
      --gst-text-2xl: ${settings.typography.h4Size};
      --gst-text-xl: ${settings.typography.h5Size};
      --gst-text-lg: ${settings.typography.h6Size};
      --gst-text-base: ${settings.typography.bodySize};
      --gst-leading-normal: ${settings.typography.lineHeight};
      
      /* Layout */
      --gst-container-max-width: ${settings.layout.containerMaxWidth};
      --gst-border-radius: ${settings.layout.borderRadius};
      --gst-spacing: ${settings.layout.spacing};
    }
  `;
};

export const applyCSSVariables = (cssVars: string): void => {
  let styleEl = document.getElementById('gst-theme-variables');
  if (!styleEl) {
    styleEl = document.createElement('style');
    styleEl.id = 'gst-theme-variables';
    document.head.appendChild(styleEl);
  }
  styleEl.textContent = cssVars;
};
