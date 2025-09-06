import React, { useState } from 'react';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Switch } from '@/components/ui/switch';
import { Badge } from '@/components/ui/badge';
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { Plus, X, Globe, Edit2 } from 'lucide-react';
import { toast } from 'sonner';
import { useLanguages } from '@/hooks/useLanguages';

interface Language {
  name: string;
  code: string;
  flag: string;
  country: string;
  is_default: boolean;
  active: boolean;
}

export const SimpleLanguageSettings: React.FC = () => {
  const { languages, switcherEnabled, loading, error, saveLanguages } = useLanguages();
  const [newLanguage, setNewLanguage] = useState({
    name: '',
    code: '',
    flag: ''
  });
  const [editingLanguage, setEditingLanguage] = useState<string | null>(null);
  const [saving, setSaving] = useState(false);
  const [sheetOpen, setSheetOpen] = useState(false);

  const handleSaveLanguages = async (updatedLanguages: Record<string, Language>, switcherEnabled?: boolean) => {
    setSaving(true);
    try {
      await saveLanguages(updatedLanguages, switcherEnabled ?? false);
      toast.success('Languages saved successfully!');
    } catch (error) {
      console.error('Error saving languages:', error);
      toast.error('Failed to save languages');
    } finally {
      setSaving(false);
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
        country: newLanguage.name,
        is_default: isFirstLanguage,
        active: true
      }
    };

    setNewLanguage({ name: '', code: '', flag: '' });
    setSheetOpen(false);
    await handleSaveLanguages(updatedLanguages);
  };

  const editLanguage = async () => {
    if (!editingLanguage || !newLanguage.name || !newLanguage.code || !newLanguage.flag) {
      toast.error('Please fill in all fields');
      return;
    }

    const code = newLanguage.code.toLowerCase();
    const originalCode = editingLanguage;
    
    let updatedLanguages;
    if (originalCode !== code) {
      // Code changed - remove old and add new
      updatedLanguages = { ...languages };
      delete updatedLanguages[originalCode];
      updatedLanguages[code] = {
        name: newLanguage.name,
        code: code,
        flag: newLanguage.flag,
        country: newLanguage.name,
        is_default: languages[originalCode]?.is_default || false,
        active: languages[originalCode]?.active || true
      };
    } else {
      // Just update existing
      updatedLanguages = {
        ...languages,
        [code]: {
          ...languages[code],
          name: newLanguage.name,
          flag: newLanguage.flag,
          country: newLanguage.name
        }
      };
    }

    setNewLanguage({ name: '', code: '', flag: '' });
    setEditingLanguage(null);
    setSheetOpen(false);
    await handleSaveLanguages(updatedLanguages);
  };

  const startEditLanguage = (code: string) => {
    const lang = languages[code];
    if (lang) {
      setEditingLanguage(code);
      setNewLanguage({
        name: lang.name,
        code: lang.code,
        flag: lang.flag
      });
      setSheetOpen(true);
    }
  };

  const cancelEdit = () => {
    setEditingLanguage(null);
    setNewLanguage({ name: '', code: '', flag: '' });
    setSheetOpen(false);
  };

  const toggleLanguageActive = async (code: string) => {
    const updatedLanguages = {
      ...languages,
      [code]: { ...languages[code], active: !languages[code].active }
    };
    await handleSaveLanguages(updatedLanguages);
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

    // If removed language was default, set new default
    if (languages[codeToRemove]?.is_default && Object.keys(updatedLanguages).length > 0) {
      const firstCode = Object.keys(updatedLanguages)[0];
      updatedLanguages[firstCode].is_default = true;
    }

    await handleSaveLanguages(updatedLanguages);
  };

  const setAsDefault = async (codeToDefault: string) => {
    if (languages[codeToDefault]?.is_default) {
      toast.info('This is already the default language.');
      return;
    }

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

    await handleSaveLanguages(updatedLanguages);
  };

  const toggleSwitcher = async (enabled: boolean) => {
    await handleSaveLanguages(languages, enabled);
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center p-4">
        <div className="animate-spin rounded-full h-6 w-6 border-b-2 border-primary"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-red-500 text-center p-4">
        Error loading languages: {error.message}
      </div>
    );
  }

  return (
    <div className="space-y-4">
      {/* Enable Language Switcher Toggle */}
      <div className="flex items-center space-x-3">
        <Switch 
          id="enable-switcher" 
          checked={switcherEnabled} 
          onCheckedChange={toggleSwitcher}
          disabled={saving}
        />
        <Label htmlFor="enable-switcher" className="text-base font-medium">
          Enable Language Switcher
        </Label>
      </div>

      {/* Languages List */}
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
                    <div 
                      className="text-lg w-6 h-6 flex items-center justify-center"
                      dangerouslySetInnerHTML={{ __html: lang.flag }}
                    />
                    <div>
                      <p className="font-medium text-sm">{lang.name}</p>
                      <p className="text-xs text-muted-foreground">
                        {code}
                        {lang.is_default && <Badge variant="default" className="ml-2 text-xs">Default</Badge>}
                      </p>
                    </div>
                  </div>
                  <div className="flex items-center space-x-1">
                    <Button
                      onClick={() => startEditLanguage(code)}
                      variant="outline"
                      size="sm"
                      className="h-6 w-6 p-0 text-blue-600 hover:text-blue-700 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                    >
                      <Edit2 className="h-3 w-3" />
                    </Button>
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
                      disabled={lang.is_default || saving}
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
          <Button className="w-full" disabled={saving}>
            <Plus className="h-4 w-4 mr-2" />
            {Object.keys(languages).length === 0 ? 'Add First Language' : `Add Next Language (${Object.keys(languages).length} existing)`}
          </Button>
        </SheetTrigger>
        <SheetContent className="w-[400px] sm:w-[540px]">
          <SheetHeader>
            <SheetTitle>{editingLanguage ? 'Edit Language' : 'Add New Language'}</SheetTitle>
            <SheetDescription>
              {editingLanguage 
                ? 'Update the language details below.' 
                : 'Add a new language to your website. The first language will become the default.'
              }
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
                <Label htmlFor="lang-flag">Flag (Emoji, SVG, or Image) *</Label>
                <Input
                  id="lang-flag"
                  placeholder="🇪🇸 or <svg>...</svg> or <img src='...' />"
                  value={newLanguage.flag}
                  onChange={(e) => setNewLanguage(prev => ({ ...prev, flag: e.target.value }))}
                  className="text-sm font-mono"
                  maxLength={1000}
                />
                <p className="text-xs text-muted-foreground">
                  Supports: Emoji (🇪🇸), SVG code, or HTML image tags
                </p>
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
                  disabled={!!editingLanguage}
                />
                {editingLanguage && (
                  <p className="text-xs text-muted-foreground">
                    Language code cannot be changed when editing
                  </p>
                )}
              </div>
            </div>
            
            <div className="text-xs text-muted-foreground">
              URL will be: <code className="bg-muted px-1 rounded">/{newLanguage.code || 'code'}/</code>
            </div>
            
            <div className="flex space-x-2">
              <Button 
                onClick={editingLanguage ? editLanguage : addLanguage} 
                disabled={saving || !newLanguage.code || !newLanguage.name || !newLanguage.flag}
                className="flex-1"
              >
                <Plus className="h-4 w-4 mr-2" />
                {saving ? (editingLanguage ? 'Updating...' : 'Adding...') : (editingLanguage ? 'Update Language' : 'Add Language')}
              </Button>
              {editingLanguage && (
                <Button 
                  onClick={cancelEdit}
                  variant="outline"
                  disabled={saving}
                >
                  Cancel
                </Button>
              )}
            </div>
          </div>
        </SheetContent>
      </Sheet>
    </div>
  );
};
