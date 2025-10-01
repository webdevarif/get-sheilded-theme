import React, { useState, useEffect, useCallback } from 'react';
import { X, Globe, Save, RotateCcw, Zap } from 'lucide-react';

const TranslationEditor = ({ 
  isVisible, 
  onClose, 
  originalText, 
  context, 
  currentLanguage, 
  availableLanguages, 
  defaultLanguage,
  onSave,
  onAutoTranslate 
}) => {
  const [translations, setTranslations] = useState({});
  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);

  // Load existing translations when component mounts or originalText changes
  useEffect(() => {
    if (originalText && context) {
      loadExistingTranslations();
    }
  }, [originalText, context]);

  const loadExistingTranslations = useCallback(async () => {
    setLoading(true);
    const translationPromises = Object.keys(availableLanguages)
      .filter(lang => availableLanguages[lang].active)
      .map(async (lang) => {
        try {
          const response = await fetch(window.gstTranslate.ajax_url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
              action: 'get_translations',
              language: lang,
              nonce: window.gstTranslate.nonce
            })
          });
          
          const data = await response.json();
          if (data.success && data.data.translations) {
            const existingTranslation = data.data.translations.find(t => 
              t.original_text === originalText && t.context === context
            );
            return { lang, translation: existingTranslation?.translation || '' };
          }
        } catch (error) {
          console.error(`Error loading translation for ${lang}:`, error);
        }
        return { lang, translation: '' };
      });

    const results = await Promise.all(translationPromises);
    const translationMap = {};
    results.forEach(({ lang, translation }) => {
      translationMap[lang] = translation;
    });
    
    setTranslations(translationMap);
    setLoading(false);
  }, [originalText, context, availableLanguages]);

  const handleTranslationChange = (language, value) => {
    setTranslations(prev => ({
      ...prev,
      [language]: value
    }));
  };

  const handleSave = async () => {
    setSaving(true);
    
    try {
      const savePromises = Object.entries(translations)
        .filter(([lang, translation]) => translation.trim())
        .map(async ([lang, translation]) => {
          const response = await fetch(window.gstTranslate.ajax_url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
              action: 'save_translation',
              original: originalText,
              translation: translation,
              language: lang,
              context: context,
              nonce: window.gstTranslate.nonce
            })
          });
          
          return response.json();
        });

      await Promise.all(savePromises);
      onSave && onSave();
      onClose();
    } catch (error) {
      console.error('Error saving translations:', error);
    } finally {
      setSaving(false);
    }
  };

  const handleAutoTranslate = async (language) => {
    if (!translations[language]?.trim()) return;
    
    try {
      const response = await fetch(window.gstTranslate.ajax_url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          action: 'auto_translate',
          text: originalText,
          language: language,
          nonce: window.gstTranslate.nonce
        })
      });
      
      const data = await response.json();
      if (data.success && data.data.translation) {
        handleTranslationChange(language, data.data.translation);
      }
    } catch (error) {
      console.error('Error auto-translating:', error);
    }
  };

  if (!isVisible) {
    console.log('TranslationEditor not visible');
    return null;
  }
  
  console.log('TranslationEditor rendering with props:', {
    isVisible,
    originalText,
    context,
    currentLanguage,
    availableLanguages,
    defaultLanguage
  });

  const currentLang = availableLanguages[currentLanguage];
  const defaultLang = availableLanguages[defaultLanguage];
  const otherLanguages = Object.entries(availableLanguages)
    .filter(([code, lang]) => lang.active && code !== currentLanguage && code !== defaultLanguage);

  return (
    <div className="fixed inset-0 z-50 bg-black bg-opacity-50 flex">
      <div className="w-96 bg-white shadow-2xl flex flex-col">
        {/* Header */}
        <div className="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
          <h3 className="text-lg font-semibold text-gray-900">Translation Editor</h3>
          <button
            onClick={onClose}
            className="p-2 hover:bg-gray-200 rounded-full transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Body */}
        <div className="flex-1 overflow-y-auto p-4 space-y-6">
          <div className="text-center">
            <p className="text-sm text-gray-600">Click on any text to translate it</p>
          </div>

          {/* Source Language */}
          <div className="space-y-2">
            <div className="flex items-center space-x-2">
              <Globe className="w-4 h-4 text-gray-500" />
              <span className="text-sm font-medium text-gray-700">
                From {defaultLang?.name || 'Source'}
              </span>
            </div>
            <input
              type="text"
              value={originalText || 'Select text to translate...'}
              readOnly
              className="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700"
            />
            <p className="text-xs text-gray-500">Text</p>
          </div>

          {/* Current Language Translation */}
          <div className="space-y-2">
            <div className="flex items-center justify-between">
              <div className="flex items-center space-x-2">
                <span className="text-lg">{currentLang?.flag || '🌐'}</span>
                <span className="text-sm font-medium text-gray-700">
                  To {currentLang?.name || currentLanguage}
                </span>
              </div>
              <button
                onClick={() => handleAutoTranslate(currentLanguage)}
                className="flex items-center space-x-1 px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors"
              >
                <Zap className="w-3 h-3" />
                <span>Auto</span>
              </button>
            </div>
            <textarea
              value={translations[currentLanguage] || ''}
              onChange={(e) => handleTranslationChange(currentLanguage, e.target.value)}
              placeholder="Enter translation..."
              className="w-full p-3 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              rows={3}
            />
            <p className="text-xs text-gray-500">Text</p>
          </div>

          {/* Other Languages */}
          {otherLanguages.map(([code, lang]) => (
            <div key={code} className="space-y-2 border-t border-gray-200 pt-4">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-2">
                  <span className="text-lg">{lang.flag}</span>
                  <span className="text-sm font-medium text-gray-700">{lang.name}</span>
                </div>
                <button
                  onClick={() => handleAutoTranslate(code)}
                  className="flex items-center space-x-1 px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors"
                >
                  <Zap className="w-3 h-3" />
                  <span>Auto</span>
                </button>
              </div>
              <textarea
                value={translations[code] || ''}
                onChange={(e) => handleTranslationChange(code, e.target.value)}
                placeholder="Enter translation..."
                className="w-full p-3 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                rows={3}
              />
            </div>
          ))}
        </div>

        {/* Footer */}
        <div className="p-4 border-t border-gray-200 bg-gray-50">
          <div className="flex space-x-3">
            <button
              onClick={onClose}
              className="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Cancel
            </button>
            <button
              onClick={handleSave}
              disabled={saving || loading}
              className="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center space-x-2"
            >
              {saving ? (
                <>
                  <RotateCcw className="w-4 h-4 animate-spin" />
                  <span>Saving...</span>
                </>
              ) : (
                <>
                  <Save className="w-4 h-4" />
                  <span>Save Translation</span>
                </>
              )}
            </button>
          </div>
        </div>
      </div>

      {/* Website iframe */}
      <div className="flex-1 bg-white">
        <iframe
          id="gst-website-iframe"
          src={`${window.location.href.replace('?edit_trans=1', '')}?edit_trans=1&iframe=1`}
          className="w-full h-full border-0"
        />
      </div>
    </div>
  );
};

export default TranslationEditor;
