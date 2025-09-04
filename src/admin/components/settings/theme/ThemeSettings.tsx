import React, { useState } from 'react';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Palette, Type, Layout, Globe } from 'lucide-react';
import Select from 'react-select';
import { Button } from '@/components/ui/button';
import { ColorPicker } from './ColorPicker';
import { LanguageSettings, LanguageToggle } from './LanguageSettings';
import { ThemeSettings as ThemeSettingsType, fontFamilies } from '../../../services/api';

interface LanguageManagementInterfaceProps {
  languages: Record<string, any>;
  onLanguagesChange: (languages: Record<string, any>) => void;
}

const LanguageManagementInterface: React.FC<LanguageManagementInterfaceProps> = ({ languages, onLanguagesChange }) => {
  return <LanguageSettings onLanguagesChange={onLanguagesChange} />;
};

interface ThemeSettingsProps {
  settings: ThemeSettingsType;
  onUpdateColor: (key: keyof ThemeSettingsType['colors'], value: string) => void;
  onUpdateTypography: (key: keyof ThemeSettingsType['typography'], value: string) => void;
  onUpdateLayout: (key: keyof ThemeSettingsType['layout'], value: string) => void;
}

export const ThemeSettings: React.FC<ThemeSettingsProps> = ({
  settings,
  onUpdateColor,
  onUpdateTypography,
  onUpdateLayout,
}) => {
  const [activeAccordion, setActiveAccordion] = useState<string>('');

  const handleLanguagesChange = (newLanguages: Record<string, any>) => {
    // Languages are managed separately through the dedicated language API
    // This function is kept for compatibility but doesn't need to do anything
    console.log('Languages changed:', newLanguages);
  };

  const renderPreview = () => {
    if (activeAccordion === 'languages') {
      return <LanguageManagementInterface languages={settings.languages || {}} onLanguagesChange={handleLanguagesChange} />;
    }

    // Default preview for other accordions
    return (
      <div className="space-y-6">
        <h2 className="text-xl font-semibold">Live Preview</h2>
        
        {/* Typography Preview */}
        <div className="space-y-4">
          <h3 className="text-lg font-medium">Typography</h3>
          <h1 style={{ fontFamily: `var(--gst-font-heading, ${settings.typography.headingFontFamily})`, fontSize: settings.typography.h1Size }}>
            Heading 1 - Main Title
          </h1>
          <h2 style={{ fontFamily: `var(--gst-font-heading, ${settings.typography.headingFontFamily})`, fontSize: settings.typography.h2Size }}>
            Heading 2 - Section Title
          </h2>
          <h3 style={{ fontFamily: `var(--gst-font-heading, ${settings.typography.headingFontFamily})`, fontSize: settings.typography.h3Size }}>
            Heading 3 - Subsection
          </h3>
          <p style={{ 
            fontFamily: `var(--gst-font-body, ${settings.typography.bodyFontFamily})`, 
            fontSize: settings.typography.bodySize,
            lineHeight: settings.typography.lineHeight 
          }}>
            This is a sample paragraph showing how your body text will look. It demonstrates the font family, size, and line height you've selected. The text should be readable and well-proportioned.
          </p>
        </div>

        <hr className="border-border" />

        {/* Color Preview */}
        <div className="space-y-4">
          <h3 className="text-lg font-medium">Color Palette</h3>
          <div className="grid grid-cols-2 gap-2">
            <div className="flex items-center gap-2">
              <div 
                className="w-6 h-6 rounded border" 
                style={{ backgroundColor: settings.colors.primary }}
              ></div>
              <span className="text-sm">Primary</span>
            </div>
            <div className="flex items-center gap-2">
              <div 
                className="w-6 h-6 rounded border" 
                style={{ backgroundColor: settings.colors.secondary }}
              ></div>
              <span className="text-sm">Secondary</span>
            </div>
            <div className="flex items-center gap-2">
              <div 
                className="w-6 h-6 rounded border" 
                style={{ backgroundColor: settings.colors.accent }}
              ></div>
              <span className="text-sm">Accent</span>
            </div>
            <div className="flex items-center gap-2">
              <div 
                className="w-6 h-6 rounded border" 
                style={{ backgroundColor: settings.colors.background }}
              ></div>
              <span className="text-sm">Background</span>
            </div>
          </div>
        </div>

        <hr className="border-border" />

        {/* Layout Preview */}
        <div className="space-y-4">
          <h3 className="text-lg font-medium">Layout</h3>
          <div 
            className="p-4 border rounded" 
            style={{ 
              maxWidth: settings.layout.containerMaxWidth,
              borderRadius: settings.layout.borderRadius,
              gap: settings.layout.spacing
            }}
          >
            <div className="p-2 bg-muted rounded text-sm">
              Container with max-width: {settings.layout.containerMaxWidth}
            </div>
            <div className="p-2 bg-muted rounded text-sm">
              Border radius: {settings.layout.borderRadius}
            </div>
          </div>
        </div>
      </div>
    );
  };
  return (
    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {/* Left Sidebar - Accordion Settings */}
      <div className="lg:col-span-1">
        <Accordion 
          type="single" 
          collapsible
          className="space-y-2"
          value={activeAccordion}
          onValueChange={setActiveAccordion}
        >
          {/* Color Palette Section */}
          <AccordionItem value="colors" className="border rounded-lg px-4 bg-white">
            <AccordionTrigger className="hover:no-underline">
              <div className="flex items-center gap-2">
                <Palette className="h-4 w-4" />
                Color Palette
              </div>
            </AccordionTrigger>
            <AccordionContent className="space-y-3 pt-2">
              {Object.entries(settings.colors).map(([key, value]) => (
                <ColorPicker
                  key={key}
                  label={key.charAt(0).toUpperCase() + key.slice(1).replace(/([A-Z])/g, ' $1')}
                  value={value}
                  onChange={(newValue) => onUpdateColor(key as keyof ThemeSettingsType['colors'], newValue)}
                />
              ))}
            </AccordionContent>
          </AccordionItem>

          {/* Typography Section */}
          <AccordionItem value="typography" className="border rounded-lg px-4 bg-white">
            <AccordionTrigger className="hover:no-underline">
              <div className="flex items-center gap-2">
                <Type className="h-4 w-4" />
                Typography
              </div>
            </AccordionTrigger>
            <AccordionContent className="space-y-4 pt-2">
              <div className="space-y-3">
                <div className="space-y-2">
                  <Label htmlFor="bodyFontFamily">Body Font</Label>
                  <Select
                    value={fontFamilies.find(font => font.value === settings.typography.bodyFontFamily)}
                    onChange={(selectedOption) => onUpdateTypography('bodyFontFamily', selectedOption?.value || '')}
                    options={fontFamilies}
                    placeholder="Select body font"
                    isSearchable
                    className="text-sm"
                    styles={{
                      control: (base, state) => ({
                        ...base,
                        minHeight: '40px',
                        borderColor: state.isFocused ? 'hsl(var(--ring))' : 'hsl(var(--border))',
                        boxShadow: state.isFocused ? '0 0 0 2px hsl(var(--ring) / 0.2)' : 'none',
                        '&:hover': {
                          borderColor: 'hsl(var(--ring))',
                        },
                        backgroundColor: 'hsl(var(--background))',
                      }),
                      placeholder: (base) => ({
                        ...base,
                        color: 'hsl(var(--muted-foreground))',
                      }),
                      singleValue: (base) => ({
                        ...base,
                        color: 'hsl(var(--foreground))',
                      }),
                      input: (base) => ({
                        ...base,
                        color: 'hsl(var(--foreground))',
                      }),
                      menu: (base) => ({
                        ...base,
                        backgroundColor: 'hsl(var(--popover))',
                        border: '1px solid hsl(var(--border))',
                        boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
                      }),
                      option: (base, state) => ({
                        ...base,
                        backgroundColor: state.isSelected 
                          ? 'hsl(var(--primary))' 
                          : state.isFocused 
                            ? 'hsl(var(--accent))' 
                            : 'transparent',
                        color: state.isSelected 
                          ? 'hsl(var(--primary-foreground))' 
                          : 'hsl(var(--foreground))',
                        '&:hover': {
                          backgroundColor: state.isSelected 
                            ? 'hsl(var(--primary))' 
                            : 'hsl(var(--accent))',
                        },
                      }),
                    }}
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="headingFontFamily">Heading Font</Label>
                  <Select
                    value={fontFamilies.find(font => font.value === settings.typography.headingFontFamily)}
                    onChange={(selectedOption) => onUpdateTypography('headingFontFamily', selectedOption?.value || '')}
                    options={fontFamilies}
                    placeholder="Select heading font"
                    isSearchable
                    className="text-sm"
                    styles={{
                      control: (base, state) => ({
                        ...base,
                        minHeight: '40px',
                        borderColor: state.isFocused ? 'hsl(var(--ring))' : 'hsl(var(--border))',
                        boxShadow: state.isFocused ? '0 0 0 2px hsl(var(--ring) / 0.2)' : 'none',
                        '&:hover': {
                          borderColor: 'hsl(var(--ring))',
                        },
                        backgroundColor: 'hsl(var(--background))',
                      }),
                      placeholder: (base) => ({
                        ...base,
                        color: 'hsl(var(--muted-foreground))',
                      }),
                      singleValue: (base) => ({
                        ...base,
                        color: 'hsl(var(--foreground))',
                      }),
                      input: (base) => ({
                        ...base,
                        color: 'hsl(var(--foreground))',
                      }),
                      menu: (base) => ({
                        ...base,
                        backgroundColor: 'hsl(var(--popover))',
                        border: '1px solid hsl(var(--border))',
                        boxShadow: '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
                      }),
                      option: (base, state) => ({
                        ...base,
                        backgroundColor: state.isSelected 
                          ? 'hsl(var(--primary))' 
                          : state.isFocused 
                            ? 'hsl(var(--accent))' 
                            : 'transparent',
                        color: state.isSelected 
                          ? 'hsl(var(--primary-foreground))' 
                          : 'hsl(var(--foreground))',
                        '&:hover': {
                          backgroundColor: state.isSelected 
                            ? 'hsl(var(--primary))' 
                            : 'hsl(var(--accent))',
                        },
                      }),
                    }}
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="lineHeight">Line Height</Label>
                  <Input
                    id="lineHeight"
                    type="text"
                    value={settings.typography.lineHeight}
                    onChange={(e) => onUpdateTypography('lineHeight', e.target.value)}
                    placeholder="1.5"
                  />
                </div>
              </div>

              <Separator />

              <div className="space-y-3">
                <h4 className="text-sm font-medium">Heading Sizes</h4>
                <div className="grid grid-cols-2 gap-2">
                  {(['h1Size', 'h2Size', 'h3Size', 'h4Size', 'h5Size', 'h6Size'] as const).map((heading) => (
                    <div key={heading} className="space-y-1">
                      <Label htmlFor={heading} className="text-xs">
                        {heading.replace('Size', '').toUpperCase()}
                      </Label>
                      <Input
                        id={heading}
                        type="text"
                        value={settings.typography[heading]}
                        onChange={(e) => onUpdateTypography(heading, e.target.value)}
                        placeholder="2rem"
                        className="h-8 text-xs"
                      />
                    </div>
                  ))}
                </div>
              </div>

              <div className="space-y-2">
                <Label htmlFor="bodySize" className="text-xs">Body Size</Label>
                <Input
                  id="bodySize"
                  type="text"
                  value={settings.typography.bodySize}
                  onChange={(e) => onUpdateTypography('bodySize', e.target.value)}
                  placeholder="0.875rem"
                  className="h-8 text-xs"
                />
              </div>
            </AccordionContent>
          </AccordionItem>

          {/* Layout Section */}
          <AccordionItem value="layout" className="border rounded-lg px-4 bg-white">
            <AccordionTrigger className="hover:no-underline">
              <div className="flex items-center gap-2">
                <Layout className="h-4 w-4" />
                Layout
              </div>
            </AccordionTrigger>
            <AccordionContent className="space-y-3 pt-2">
              <div className="space-y-2">
                <Label htmlFor="containerMaxWidth">Container Width</Label>
                <Input
                  id="containerMaxWidth"
                  type="text"
                  value={settings.layout.containerMaxWidth}
                  onChange={(e) => onUpdateLayout('containerMaxWidth', e.target.value)}
                  placeholder="1200px"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="borderRadius">Border Radius</Label>
                <Input
                  id="borderRadius"
                  type="text"
                  value={settings.layout.borderRadius}
                  onChange={(e) => onUpdateLayout('borderRadius', e.target.value)}
                  placeholder="0.5rem"
                />
              </div>

              <div className="space-y-2">
                <Label htmlFor="spacing">Base Spacing</Label>
                <Input
                  id="spacing"
                  type="text"
                  value={settings.layout.spacing}
                  onChange={(e) => onUpdateLayout('spacing', e.target.value)}
                  placeholder="1rem"
                />
              </div>
            </AccordionContent>
          </AccordionItem>

          {/* Languages Section */}
          <AccordionItem value="languages" className="border rounded-lg px-4 bg-white">
            <AccordionTrigger className="hover:no-underline">
              <div className="flex items-center gap-2">
                <Globe className="h-4 w-4" />
                Languages
              </div>
            </AccordionTrigger>
              <AccordionContent className="space-y-3 pt-2">
                <LanguageToggle onLanguagesChange={handleLanguagesChange} initialLanguages={settings.languages || {}} />
              </AccordionContent>
          </AccordionItem>
        </Accordion>
      </div>

      {/* Right Side - Preview */}
      <div className="lg:col-span-2">
        <div className="space-y-6 p-6 border rounded-lg bg-background">
          {renderPreview()}
        </div>
      </div>
    </div>
  );
};
