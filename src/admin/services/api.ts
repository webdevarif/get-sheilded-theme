import { toast } from 'sonner';

export interface ThemeSettings {
  colors: {
    primary: string;
    secondary: string;
    accent: string;
    background: string;
    foreground: string;
    card: string;
    cardForeground: string;
    popover: string;
    popoverForeground: string;
    muted: string;
    mutedForeground: string;
    border: string;
    input: string;
    ring: string;
    destructive: string;
    destructiveForeground: string;
  };
  typography: {
    bodyFontFamily: string;
    headingFontFamily: string;
    h1Size: string;
    h2Size: string;
    h3Size: string;
    h4Size: string;
    h5Size: string;
    h6Size: string;
    bodySize: string;
    lineHeight: string;
  };
  layout: {
    containerMaxWidth: string;
    borderRadius: string;
    spacing: string;
  };
}

export const defaultSettings: ThemeSettings = {
  colors: {
    primary: 'hsl(222.2, 84%, 4.9%)',
    secondary: 'hsl(210, 40%, 96%)',
    accent: 'hsl(210, 40%, 96%)',
    background: 'hsl(0, 0%, 100%)',
    foreground: 'hsl(222.2, 84%, 4.9%)',
    card: 'hsl(0, 0%, 100%)',
    cardForeground: 'hsl(222.2, 84%, 4.9%)',
    popover: 'hsl(0, 0%, 100%)',
    popoverForeground: 'hsl(222.2, 84%, 4.9%)',
    muted: 'hsl(210, 40%, 96%)',
    mutedForeground: 'hsl(215.4, 16.3%, 46.9%)',
    border: 'hsl(214.3, 31.8%, 91.4%)',
    input: 'hsl(214.3, 31.8%, 91.4%)',
    ring: 'hsl(222.2, 84%, 4.9%)',
    destructive: 'hsl(0, 84.2%, 60.2%)',
    destructiveForeground: 'hsl(210, 40%, 98%)',
  },
  typography: {
    bodyFontFamily: 'Inter, system-ui, sans-serif',
    headingFontFamily: 'Inter, system-ui, sans-serif',
    h1Size: '2.25rem',
    h2Size: '1.875rem',
    h3Size: '1.5rem',
    h4Size: '1.25rem',
    h5Size: '1.125rem',
    h6Size: '1rem',
    bodySize: '0.875rem',
    lineHeight: '1.5',
  },
  layout: {
    containerMaxWidth: '1200px',
    borderRadius: '0.5rem',
    spacing: '1rem',
  },
};

export const fontFamilies = [
  // System Fonts
  { label: 'System Default', value: 'system-ui, -apple-system, sans-serif' },
  { label: 'Arial', value: 'Arial, sans-serif' },
  { label: 'Helvetica', value: 'Helvetica, sans-serif' },
  { label: 'Georgia', value: 'Georgia, serif' },
  { label: 'Times New Roman', value: 'Times New Roman, serif' },
  
  // Popular Google Fonts - Sans Serif
  { label: 'Inter (Google)', value: 'Inter, system-ui, sans-serif' },
  { label: 'Roboto (Google)', value: 'Roboto, sans-serif' },
  { label: 'Open Sans (Google)', value: 'Open Sans, sans-serif' },
  { label: 'Lato (Google)', value: 'Lato, sans-serif' },
  { label: 'Montserrat (Google)', value: 'Montserrat, sans-serif' },
  { label: 'Poppins (Google)', value: 'Poppins, sans-serif' },
  { label: 'Source Sans Pro (Google)', value: 'Source Sans Pro, sans-serif' },
  { label: 'Nunito (Google)', value: 'Nunito, sans-serif' },
  { label: 'Raleway (Google)', value: 'Raleway, sans-serif' },
  { label: 'Ubuntu (Google)', value: 'Ubuntu, sans-serif' },
  { label: 'Work Sans (Google)', value: 'Work Sans, sans-serif' },
  { label: 'DM Sans (Google)', value: 'DM Sans, sans-serif' },
  { label: 'Fira Sans (Google)', value: 'Fira Sans, sans-serif' },
  { label: 'PT Sans (Google)', value: 'PT Sans, sans-serif' },
  { label: 'Source Sans 3 (Google)', value: 'Source Sans 3, sans-serif' },
  { label: 'Noto Sans (Google)', value: 'Noto Sans, sans-serif' },
  { label: 'Cabin (Google)', value: 'Cabin, sans-serif' },
  { label: 'Dosis (Google)', value: 'Dosis, sans-serif' },
  { label: 'Exo 2 (Google)', value: 'Exo 2, sans-serif' },
  { label: 'Hind (Google)', value: 'Hind, sans-serif' },
  { label: 'Josefin Sans (Google)', value: 'Josefin Sans, sans-serif' },
  { label: 'Libre Franklin (Google)', value: 'Libre Franklin, sans-serif' },
  { label: 'Maven Pro (Google)', value: 'Maven Pro, sans-serif' },
  { label: 'Oxygen (Google)', value: 'Oxygen, sans-serif' },
  { label: 'Quicksand (Google)', value: 'Quicksand, sans-serif' },
  { label: 'Titillium Web (Google)', value: 'Titillium Web, sans-serif' },
  
  // Popular Google Fonts - Serif
  { label: 'Playfair Display (Google)', value: 'Playfair Display, serif' },
  { label: 'Merriweather (Google)', value: 'Merriweather, serif' },
  { label: 'Lora (Google)', value: 'Lora, serif' },
  { label: 'Crimson Text (Google)', value: 'Crimson Text, serif' },
  { label: 'PT Serif (Google)', value: 'PT Serif, serif' },
  { label: 'Source Serif Pro (Google)', value: 'Source Serif Pro, serif' },
  { label: 'Libre Baskerville (Google)', value: 'Libre Baskerville, serif' },
  { label: 'Crimson Pro (Google)', value: 'Crimson Pro, serif' },
  { label: 'EB Garamond (Google)', value: 'EB Garamond, serif' },
  { label: 'Noto Serif (Google)', value: 'Noto Serif, serif' },
  
  // Popular Google Fonts - Display/Headings
  { label: 'Oswald (Google)', value: 'Oswald, sans-serif' },
  { label: 'Bebas Neue (Google)', value: 'Bebas Neue, sans-serif' },
  { label: 'Anton (Google)', value: 'Anton, sans-serif' },
  { label: 'Righteous (Google)', value: 'Righteous, sans-serif' },
  { label: 'Fredoka One (Google)', value: 'Fredoka One, sans-serif' },
  { label: 'Bangers (Google)', value: 'Bangers, sans-serif' },
  { label: 'Creepster (Google)', value: 'Creepster, sans-serif' },
  { label: 'Lobster (Google)', value: 'Lobster, sans-serif' },
  { label: 'Pacifico (Google)', value: 'Pacifico, sans-serif' },
  { label: 'Dancing Script (Google)', value: 'Dancing Script, sans-serif' },
  
  // Monospace
  { label: 'Fira Code (Google)', value: 'Fira Code, monospace' },
  { label: 'Source Code Pro (Google)', value: 'Source Code Pro, monospace' },
  { label: 'JetBrains Mono (Google)', value: 'JetBrains Mono, monospace' },
  { label: 'Roboto Mono (Google)', value: 'Roboto Mono, monospace' },
];

class SettingsAPI {
  private getApiUrl(): string {
    return (window as any).gstAdminData?.apiUrl || '/wp-json/gst/v1/';
  }

  private getNonce(): string {
    return (window as any).gstAdminData?.nonce || '';
  }

  async loadSettings(): Promise<ThemeSettings> {
    try {
      console.log('Loading settings from:', `${this.getApiUrl()}settings`);
      console.log('Using nonce:', this.getNonce());
      
      const response = await fetch(`${this.getApiUrl()}settings`, {
        headers: {
          'X-WP-Nonce': this.getNonce(),
        },
      });
      
      console.log('Response status:', response.status);
      
      if (response.ok) {
        const data = await response.json();
        console.log('Loaded settings:', data);
        const settings = data.settings || defaultSettings;
        
        // Clean up any old format data
        const cleanedSettings = this.cleanSettings(settings);
        return cleanedSettings;
      } else {
        console.error('Response not OK:', response.status, response.statusText);
        const errorText = await response.text();
        console.error('Error response:', errorText);
        return defaultSettings;
      }
    } catch (error) {
      console.error('Failed to load settings:', error);
      toast.error('Failed to load settings, using defaults');
      return defaultSettings;
    }
  }

  private cleanSettings(settings: any): ThemeSettings {
    // Remove old format keys and ensure only new format exists
    const cleaned = { ...settings };
    
    // Always ensure typography section exists with proper structure
    // Convert old format keys to new camelCase format
    cleaned.typography = {
      bodyFontFamily: cleaned.typography?.bodyFontFamily || cleaned.typography?.bodyfontfamily || defaultSettings.typography.bodyFontFamily,
      headingFontFamily: cleaned.typography?.headingFontFamily || cleaned.typography?.headingfontfamily || defaultSettings.typography.headingFontFamily,
      h1Size: cleaned.typography?.h1Size || cleaned.typography?.h1size || defaultSettings.typography.h1Size,
      h2Size: cleaned.typography?.h2Size || cleaned.typography?.h2size || defaultSettings.typography.h2Size,
      h3Size: cleaned.typography?.h3Size || cleaned.typography?.h3size || defaultSettings.typography.h3Size,
      h4Size: cleaned.typography?.h4Size || cleaned.typography?.h4size || defaultSettings.typography.h4Size,
      h5Size: cleaned.typography?.h5Size || cleaned.typography?.h5size || defaultSettings.typography.h5Size,
      h6Size: cleaned.typography?.h6Size || cleaned.typography?.h6size || defaultSettings.typography.h6Size,
      bodySize: cleaned.typography?.bodySize || cleaned.typography?.bodysize || defaultSettings.typography.bodySize,
      lineHeight: cleaned.typography?.lineHeight || cleaned.typography?.lineheight || defaultSettings.typography.lineHeight,
    };
    
    // Always ensure layout section exists with proper structure
    cleaned.layout = {
      containerMaxWidth: cleaned.layout?.containerMaxWidth || cleaned.layout?.containermaxwidth || defaultSettings.layout.containerMaxWidth,
      borderRadius: cleaned.layout?.borderRadius || cleaned.layout?.borderradius || defaultSettings.layout.borderRadius,
      spacing: cleaned.layout?.spacing || defaultSettings.layout.spacing,
    };
    
    return cleaned as ThemeSettings;
  }

  async saveSettings(settings: ThemeSettings): Promise<boolean> {
    try {
      const requestBody = { settings };
      console.log('Sending settings:', JSON.stringify(requestBody, null, 2));
      
      const response = await fetch(`${this.getApiUrl()}settings`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': this.getNonce(),
        },
        body: JSON.stringify(requestBody),
      });

      console.log('Response status:', response.status);
      console.log('Response ok:', response.ok);
      
      if (response.ok) {
        const responseData = await response.json();
        console.log('Save response:', responseData);
        toast.success('Settings saved successfully!');
        return true;
      } else {
        const errorText = await response.text();
        console.error('Failed to save settings - Status:', response.status);
        console.error('Error response:', errorText);
        toast.error(`Failed to save settings: ${response.status}`);
        return false;
      }
    } catch (error) {
      console.error('Failed to save settings:', error);
      toast.error('Failed to save settings');
      return false;
    }
  }
}

export const settingsAPI = new SettingsAPI();
