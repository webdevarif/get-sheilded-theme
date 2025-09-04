import React, { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Save } from 'lucide-react';
import { Toaster } from 'sonner';

// Import services
import { settingsAPI, ThemeSettings, defaultSettings } from './services/api';
import { generateCSSVariables, applyCSSVariables } from './services/cssGenerator';

// Import components
import { ThemeSettings as ThemeSettingsComponent } from './components/settings';

export const Settings: React.FC = () => {
  const [settings, setSettings] = useState<ThemeSettings>(defaultSettings);
  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);

  // Load settings from WordPress
  useEffect(() => {
    loadSettings();
  }, []);

  // Apply CSS variables when settings change
  useEffect(() => {
    const cssVars = generateCSSVariables(settings);
    applyCSSVariables(cssVars);
  }, [settings]);

  const loadSettings = async () => {
    setLoading(true);
    try {
      const loadedSettings = await settingsAPI.loadSettings();
      setSettings(loadedSettings);
    } catch (error) {
      console.error('Failed to load settings:', error);
    } finally {
      setLoading(false);
    }
  };

  const saveSettings = async () => {
    setSaving(true);
    try {
      const success = await settingsAPI.saveSettings(settings);
      if (success) {
        // Settings saved successfully
      }
    } catch (error) {
      console.error('Failed to save settings:', error);
    } finally {
      setSaving(false);
    }
  };

  const updateColor = (key: keyof ThemeSettings['colors'], value: string) => {
    setSettings(prev => ({
      ...prev,
      colors: {
        ...prev.colors,
        [key]: value,
      },
    }));
  };

  const updateTypography = (key: keyof ThemeSettings['typography'], value: string) => {
    setSettings(prev => ({
      ...prev,
      typography: {
        ...prev.typography,
        [key]: value,
      },
    }));
  };

  const updateLayout = (key: keyof ThemeSettings['layout'], value: string) => {
    setSettings(prev => ({
      ...prev,
      layout: {
        ...prev.layout,
        [key]: value,
      },
    }));
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="text-center">
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-4"></div>
          <p className="text-muted-foreground">Loading settings...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <Toaster />
      
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold">Theme Settings</h1>
          <p className="text-muted-foreground mt-2">
            Customize your theme's appearance and functionality using the organized settings below.
          </p>
        </div>
        <div className="flex gap-2">
          <Button onClick={saveSettings} disabled={saving}>
            <Save className="h-4 w-4 mr-2" />
            {saving ? 'Saving...' : 'Save Settings'}
          </Button>
        </div>
      </div>

      {/* Main Content - Direct Theme Settings */}
      <ThemeSettingsComponent
        settings={settings}
        onUpdateColor={updateColor}
        onUpdateTypography={updateTypography}
        onUpdateLayout={updateLayout}
      />
    </div>
  );
};

export default Settings;