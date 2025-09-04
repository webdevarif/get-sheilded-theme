import React, { useState, useEffect } from 'react';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Switch } from '@/components/ui/switch';
import { Badge } from '@/components/ui/badge';
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { Plus, X, Globe, Info, Code, ExternalLink } from 'lucide-react';
import { toast } from 'sonner';
import { LANGUAGE_ENDPOINTS, getApiHeaders } from '@/lib/api-config';

interface Language {
  name: string;
  code: string;
  flag: string;
  country: string;
  is_default: boolean;
  active: boolean;
}

interface LanguageSettingsProps {
  onLanguagesChange: (languages: Record<string, Language>) => void;
  initialLanguages?: Record<string, Language>;
}

// Simple toggle component for left accordion
export const LanguageToggle: React.FC<LanguageSettingsProps> = ({ onLanguagesChange, initialLanguages = {} }) => {
  const [languages, setLanguages] = useState<Record<string, Language>>(initialLanguages);
  const [loading, setLoading] = useState(false);
  const [newLanguage, setNewLanguage] = useState({
    name: '',
    code: '',
    flag: ''
  });
  const [saving, setSaving] = useState(false);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [switcherEnabled, setSwitcherEnabled] = useState(false);

  useEffect(() => {
    if (Object.keys(initialLanguages).length > 0) {
      setLanguages(initialLanguages);
      onLanguagesChange(initialLanguages);
      // Load switcher state from database, not based on active languages
      loadLanguages();
    } else {
      loadLanguages();
    }
  }, [initialLanguages]);

  const loadLanguages = async () => {
    try {
      const response = await fetch(LANGUAGE_ENDPOINTS.GET_LANGUAGES, {
        headers: getApiHeaders()
      });
      
      if (response.ok) {
        const data = await response.json();
        console.log('LanguageToggle - Loaded data:', data);
        setLanguages(data.languages);
        onLanguagesChange(data.languages);
        // Set switcher enabled from API response
        console.log('LanguageToggle - Setting switcher enabled to:', data.switcher_enabled);
        setSwitcherEnabled(data.switcher_enabled);
      }
    } catch (error) {
      console.error('Error loading languages:', error);
    } finally {
      setLoading(false);
    }
  };

  const loadSwitcherState = async () => {
    try {
      // For now, we'll determine switcher state based on active languages
      // In the future, we could add a separate endpoint to get the saved switcher state
      const activeCount = Object.values(languages).filter(lang => lang.active).length;
      setSwitcherEnabled(activeCount > 1);
    } catch (error) {
      console.error('Error loading switcher state:', error);
    }
  };

  const saveLanguages = async (updatedLanguages: Record<string, Language>, switcherEnabled?: boolean) => {
    try {
      const response = await fetch(LANGUAGE_ENDPOINTS.SAVE_LANGUAGES, {
        method: 'POST',
        headers: getApiHeaders(),
        body: JSON.stringify({ 
          languages: updatedLanguages,
          switcher_enabled: switcherEnabled
        })
      });

      if (response.ok) {
        const data = await response.json();
        console.log('LanguageToggle - Saved data:', data);
        setLanguages(data.languages);
        onLanguagesChange(data.languages);
        // Set switcher enabled from API response
        console.log('LanguageToggle - Setting switcher enabled to:', data.switcher_enabled);
        setSwitcherEnabled(data.switcher_enabled);
        return data.languages;
      } else {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to save languages');
      }
    } catch (error) {
      console.error('Error saving languages:', error);
      throw error;
    }
  };

  const saveSwitcherState = async (enabled: boolean) => {
    try {
      const response = await fetch(LANGUAGE_ENDPOINTS.SAVE_SWITCHER_STATE, {
        method: 'POST',
        headers: getApiHeaders(),
        body: JSON.stringify({ enabled })
      });

      if (response.ok) {
        const data = await response.json();
        return data.enabled;
      } else {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to save switcher state');
      }
    } catch (error) {
      console.error('Error saving switcher state:', error);
      throw error;
    }
  };

  const addLanguage = async () => {
    if (!newLanguage.name || !newLanguage.code || !newLanguage.flag) {
      toast.error('Please fill in all fields');
      return;
    }

    const code = newLanguage.code.toLowerCase();
    const isFirstLanguage = Object.keys(languages).length === 0;

    const updatedLanguages = {
      ...languages,
      [code]: {
        name: newLanguage.name,
        code: code,
        flag: newLanguage.flag,
        country: newLanguage.name, // Use language name as country fallback
        is_default: isFirstLanguage,
        active: true
      }
    };

    setSaving(true);
    try {
      const savedLanguages = await saveLanguages(updatedLanguages);
      setNewLanguage({ name: '', code: '', flag: '' });
      toast.success('Language added successfully!');
      // Force reload to ensure UI updates
      await loadLanguages();
      // Close the sheet
      setSheetOpen(false);
    } catch (error) {
      console.error('Error adding language:', error);
      toast.error('Failed to add language');
    } finally {
      setSaving(false);
    }
  };

  const toggleLanguageActive = async (code: string) => {
    const updatedLanguages = {
      ...languages,
      [code]: { ...languages[code], active: !languages[code].active }
    };
    await saveLanguages(updatedLanguages);
  };

  const removeLanguage = async (codeToRemove: string) => {
    if (languages[codeToRemove]?.is_default) {
      toast.error('Cannot remove default language.');
      return;
    }
    if (!confirm(`Are you sure you want to remove ${languages[codeToRemove]?.name}?`)) {
      return;
    }

    const updatedLanguages = { ...languages };
    delete updatedLanguages[codeToRemove];

    // If the removed language was the only one, ensure no default is left
    if (Object.keys(updatedLanguages).length === 0) {
      await saveLanguages({});
      setLanguages({});
      onLanguagesChange({});
      toast.success('Last language removed. No languages configured.');
      return;
    }

    // If the removed language was the default, set a new default
    if (languages[codeToRemove]?.is_default && Object.keys(updatedLanguages).length > 0) {
      const firstCode = Object.keys(updatedLanguages)[0];
      updatedLanguages[firstCode].is_default = true;
    }

    try {
      const savedLanguages = await saveLanguages(updatedLanguages);
      setLanguages(savedLanguages);
      onLanguagesChange(savedLanguages);
      toast.success('Language removed successfully!');
    } catch (error) {
      toast.error('Failed to remove language');
    }
  };

  const setAsDefault = async (codeToDefault: string) => {
    if (languages[codeToDefault]?.is_default) {
      toast.info('This is already the default language.');
      return;
    }

    // Check if the language is active
    if (!languages[codeToDefault]?.active) {
      toast.error('Cannot set a disabled language as default. Please enable the language first.');
      return;
    }

    const updatedLanguages = Object.fromEntries(
      Object.entries(languages).map(([code, lang]) => [
        code,
        { ...lang, is_default: code === codeToDefault }
      ])
    );

    try {
      const savedLanguages = await saveLanguages(updatedLanguages);
      setLanguages(savedLanguages);
      onLanguagesChange(savedLanguages);
      toast.success(`${languages[codeToDefault]?.name} set as default!`);
    } catch (error) {
      toast.error('Failed to set default language');
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center p-4">
        <div className="animate-spin rounded-full h-6 w-6 border-b-2 border-primary"></div>
      </div>
    );
  }

  const activeLanguages = Object.values(languages).filter(lang => lang.active);

  return (
    <div className="space-y-4">
      <div className="flex items-center space-x-3">
        <Switch 
          id="enable-switcher" 
          checked={switcherEnabled} 
          onCheckedChange={async (enabled) => {
            try {
              console.log('LanguageToggle - Toggle changed to:', enabled);
              console.log('LanguageToggle - Current languages:', languages);
              await saveLanguages(languages, enabled);
              if (enabled) {
                toast.success('Language switcher enabled');
              } else {
                toast.info('Language switcher disabled');
              }
            } catch (error) {
              console.error('Error toggling switcher:', error);
              toast.error('Failed to update switcher state');
            }
          }}
        />
        <Label htmlFor="enable-switcher" className="text-base font-medium">
          Enable Language Switcher
        </Label>
      </div>

      {/* Current Languages List */}
      <div className="space-y-3">
        {Object.keys(languages).length === 0 ? (
          <p className="text-muted-foreground text-sm">
            <Globe className="h-4 w-4 inline mr-1" />
            No languages configured yet
          </p>
        ) : (
          <div className="space-y-2">
            {Object.values(languages).map((lang) => {
              const code = lang.code;
              return (
                <div key={code} className="flex items-center justify-between p-3 border-b group hover:bg-gray-50 transition-colors duration-200">
                  <div className="flex items-center space-x-2">
                    <span className="text-lg">{lang.flag}</span>
                    <div>
                      <p className="font-medium text-sm">{lang.name}</p>
                      <p className="text-xs text-muted-foreground">
                        {code}
                        {lang.is_default && <Badge variant="default" className="ml-2 text-xs">Default</Badge>}
                      </p>
                    </div>
                  </div>
                  <div className="flex items-center space-x-1">
                    {!lang.is_default && lang.active && (
                      <Badge
                        onClick={() => setAsDefault(code)}
                        variant="secondary"
                        className="opacity-0 group-hover:opacity-100 transition-opacity duration-200 cursor-pointer"
                      >
                        Set as Default
                      </Badge>
                    )}
                    {!lang.is_default && (
                      <Button
                        onClick={() => removeLanguage(code)}
                        variant="outline"
                        size="sm"
                        className="h-6 w-6 p-0 text-red-600 hover:text-red-700 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                      >
                        <X className="h-3 w-3" />
                      </Button>
                    )}
                    <Switch
                      checked={lang.active}
                      onCheckedChange={() => toggleLanguageActive(code)}
                      disabled={lang.is_default}
                      className="scale-75"
                    />
                  </div>
                </div>
              );
            })}
          </div>
        )}
      </div>

      {/* Add Language Sheet */}
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetTrigger asChild>
          <Button className="w-full">
            <Plus className="h-4 w-4 mr-2" />
            {Object.keys(languages).length === 0 ? 'Add First Language' : `Add Next Language (${Object.keys(languages).length} existing)`}
          </Button>
        </SheetTrigger>
        <SheetContent className="w-[400px] sm:w-[540px]">
          <SheetHeader>
            <SheetTitle>Add New Language</SheetTitle>
            <SheetDescription>
              Add a new language to your website. The first language will become the default.
            </SheetDescription>
          </SheetHeader>
          
          <div className="space-y-6 py-6">
            <div className="space-y-2">
              <Label htmlFor="lang-name">Language *</Label>
              <Input
                id="lang-name"
                placeholder="Spanish"
                value={newLanguage.name}
                onChange={(e) => setNewLanguage(prev => ({ ...prev, name: e.target.value }))}
              />
            </div>
            
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="lang-flag">Flag Emoji *</Label>
                <Input
                  id="lang-flag"
                  placeholder="🇪🇸"
                  value={newLanguage.flag}
                  onChange={(e) => setNewLanguage(prev => ({ ...prev, flag: e.target.value }))}
                  className="text-center text-xl"
                  maxLength={2}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="lang-code">Code *</Label>
                <Input
                  id="lang-code"
                  placeholder="es"
                  value={newLanguage.code}
                  onChange={(e) => setNewLanguage(prev => ({ ...prev, code: e.target.value.toLowerCase() }))}
                  className="font-mono"
                  maxLength={5}
                />
              </div>
            </div>
            
            <div className="text-xs text-muted-foreground">
              URL will be: <code className="bg-muted px-1 rounded">/{newLanguage.code || 'code'}/</code>
            </div>
            
            <Button 
              onClick={addLanguage} 
              disabled={saving || !newLanguage.code || !newLanguage.name || !newLanguage.flag}
              className="w-full"
            >
              <Plus className="h-4 w-4 mr-2" />
              {saving ? 'Adding...' : 'Add Language'}
            </Button>
          </div>
        </SheetContent>
      </Sheet>
    </div>
  );
};

// Right side component for guides and information
export const LanguageSettings: React.FC<LanguageSettingsProps> = ({ onLanguagesChange }) => {
  const [languages, setLanguages] = useState<Record<string, Language>>({});
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadLanguages();
  }, []);

  const loadLanguages = async () => {
    try {
      const response = await fetch(LANGUAGE_ENDPOINTS.GET_LANGUAGES, {
        headers: getApiHeaders()
      });
      
      if (response.ok) {
        const data = await response.json();
        setLanguages(data.languages || data);
        onLanguagesChange(data.languages || data);
      }
    } catch (error) {
      console.error('Error loading languages:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center p-8">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
      </div>
    );
  }

  const activeLanguages = Object.values(languages).filter(lang => lang.active);

  return (
    <div className="space-y-8">
      {/* Language Switcher Preview */}
      <div>
        <h3 className="text-lg font-semibold mb-4 flex items-center gap-2">
          <Globe className="h-5 w-5" />
          Language Switcher Preview
        </h3>
      </div>

      {/* Shortcode Reference */}
      <div>
        <h3 className="text-lg font-semibold mb-4 flex items-center gap-2">
          <Code className="h-5 w-5" />
          Shortcode Reference
        </h3>
        <div className="space-y-3">
          <div>
            <p className="text-sm font-medium mb-1">Display Language Switcher:</p>
            <code className="block p-2 bg-muted rounded text-sm font-mono">
              [gst_language_switcher]
            </code>
          </div>
          <div>
            <p className="text-sm font-medium mb-1">Get Current Language:</p>
            <code className="block p-2 bg-muted rounded text-sm font-mono">
              gst_get_current_language()
            </code>
          </div>
          <div>
            <p className="text-sm font-medium mb-1">Get Language URL:</p>
            <code className="block p-2 bg-muted rounded text-sm font-mono">
              gst_get_language_url('es', '/about/')
            </code>
          </div>
        </div>
      </div>

      {/* URL Structure Guide */}
      <div>
        <h3 className="text-lg font-semibold mb-4 flex items-center gap-2">
          <ExternalLink className="h-5 w-5" />
          URL Structure
        </h3>
        <div className="space-y-3">
          <div className="p-3 border-l-4 border-blue-500 bg-blue-50">
            <p className="text-sm font-medium text-blue-900">Default Language</p>
            <p className="text-sm text-blue-700">No prefix: <code>yoursite.com/about/</code></p>
          </div>
          <div className="p-3 border-l-4 border-green-500 bg-green-50">
            <p className="text-sm font-medium text-green-900">Other Languages</p>
            <p className="text-sm text-green-700">With prefix: <code>yoursite.com/es/about/</code></p>
          </div>
        </div>
      </div>

      {/* How It Works */}
      <div>
        <h3 className="text-lg font-semibold mb-4 flex items-center gap-2">
          <Info className="h-5 w-5" />
          How It Works
        </h3>
        <div className="space-y-3 text-sm">
          <div className="flex items-start gap-3">
            <div className="w-6 h-6 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-xs font-bold">1</div>
            <div>
              <p className="font-medium">Add Languages</p>
              <p className="text-muted-foreground">Use the "Add Language" button in the left panel to add languages one by one</p>
            </div>
          </div>
          <div className="flex items-start gap-3">
            <div className="w-6 h-6 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-xs font-bold">2</div>
            <div>
              <p className="font-medium">First Language = Default</p>
              <p className="text-muted-foreground">The first language you add becomes the default with no URL prefix</p>
            </div>
          </div>
          <div className="flex items-start gap-3">
            <div className="w-6 h-6 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-xs font-bold">3</div>
            <div>
              <p className="font-medium">Additional Languages</p>
              <p className="text-muted-foreground">Other languages get URL prefixes like /es/, /fr/, etc.</p>
            </div>
          </div>
          <div className="flex items-start gap-3">
            <div className="w-6 h-6 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-xs font-bold">4</div>
            <div>
              <p className="font-medium">Automatic Switcher</p>
              <p className="text-muted-foreground">Language switcher appears automatically in Header 1 block</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  );
};
