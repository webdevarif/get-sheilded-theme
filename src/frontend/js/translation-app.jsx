import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import TranslationEditor from '../components/TranslationEditor';

const TranslationApp = () => {
  const [isVisible, setIsVisible] = useState(false);
  const [selectedText, setSelectedText] = useState('');
  const [context, setContext] = useState('content');
  const [currentElement, setCurrentElement] = useState(null);

  useEffect(() => {
    console.log('TranslationApp mounted, gstTranslate:', window.gstTranslate);
    
    // Always show the sidebar in editor mode for testing
    setIsVisible(true);
    
    // Check if we're in iframe mode
    if (window.gstTranslate?.iframe_mode) {
      console.log('Running in iframe mode');
      // Iframe mode - just make elements clickable and send messages to parent
      makeElementsClickable();
    } else {
      console.log('Running in full editor mode');
      // Full editor mode - show sidebar and iframe layout
      makeElementsClickable();
    }
  }, []);

  const makeElementsClickable = () => {
    // Clean up any invalid elements first
    document.querySelectorAll('.gst-translatable').forEach(el => {
      if (!el.parentNode) {
        el.remove();
      }
    });

    // Make translatable elements clickable
    document.querySelectorAll('.gst-translatable').forEach(el => {
      el.addEventListener('click', handleElementClick);
      
      // Add hover effects
      el.addEventListener('mouseenter', () => el.classList.add('hover'));
      el.addEventListener('mouseleave', () => el.classList.remove('hover'));
    });
  };

  const handleElementClick = (e) => {
    e.preventDefault();
    e.stopPropagation();

    if (!e.target) return;

    if (window.gstTranslate?.iframe_mode) {
      // Send message to parent window
      const originalText = e.target.dataset.original || e.target.textContent;
      const context = e.target.dataset.context || 'content';

      if (window.parent) {
        window.parent.postMessage({
          type: 'gst_text_selected',
          originalText: originalText,
          context: context,
          element: e.target.outerHTML
        }, '*');
      }
    } else {
      // Update state for React component
      const originalText = e.target.dataset.original || e.target.textContent;
      const context = e.target.dataset.context || 'content';
      
      setSelectedText(originalText);
      setContext(context);
      setCurrentElement(e.target);
      setIsVisible(true);
    }
  };

  const handleSave = () => {
    // Update the element if it's the current language
    if (currentElement && window.gstTranslate?.current_language) {
      const translation = document.querySelector(`textarea[data-language="${window.gstTranslate.current_language}"]`)?.value;
      if (translation) {
        currentElement.textContent = translation;
      }
    }
  };

  const handleClose = () => {
    setIsVisible(false);
    setSelectedText('');
    setContext('content');
    setCurrentElement(null);
  };

  // Setup iframe communication
  useEffect(() => {
    const iframe = document.getElementById('gst-website-iframe');
    
    if (iframe) {
      iframe.onload = () => {
        try {
          const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
          
          if (iframeDoc) {
            // Add click handlers to translatable elements in iframe
            iframeDoc.querySelectorAll('.gst-translatable').forEach(el => {
              el.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const originalText = el.dataset.original || el.textContent;
                const context = el.dataset.context || 'content';
                
                setSelectedText(originalText);
                setContext(context);
                setCurrentElement(el);
              });
              
              // Add hover effects
              el.addEventListener('mouseenter', () => el.classList.add('hover'));
              el.addEventListener('mouseleave', () => el.classList.remove('hover'));
            });
          }
        } catch (e) {
          console.log('Cannot access iframe content due to cross-origin restrictions');
        }
      };
    }

    // Listen for messages from iframe
    const handleMessage = (event) => {
      if (event.data.type === 'gst_text_selected') {
        setSelectedText(event.data.originalText);
        setContext(event.data.context);
        setIsVisible(true);
      }
    };

    window.addEventListener('message', handleMessage);
    return () => window.removeEventListener('message', handleMessage);
  }, []);

  if (!window.gstTranslate) {
    console.log('gstTranslate not available, using fallback data...');
    // Use fallback data for testing
    window.gstTranslate = {
      current_language: 'bn',
      available_languages: {
        'en': { name: 'English', flag: '🇺🇸', active: true },
        'bn': { name: 'Bengali', flag: '🇧🇩', active: true },
        'es': { name: 'Spanish', flag: '🇪🇸', active: true }
      },
      default_language: 'en',
      iframe_mode: false
    };
  }

  return (
    <TranslationEditor
      isVisible={isVisible}
      onClose={handleClose}
      originalText={selectedText}
      context={context}
      currentLanguage={window.gstTranslate.current_language}
      availableLanguages={window.gstTranslate.available_languages}
      defaultLanguage={window.gstTranslate.default_language}
      onSave={handleSave}
    />
  );
};

// Initialize the app
function initializeTranslationApp() {
  const container = document.getElementById('gst-translation-app');
  if (container) {
    console.log('Initializing React translation app...');
    const root = createRoot(container);
    root.render(<TranslationApp />);
  } else {
    console.log('Translation app container not found, retrying...');
    // Retry after a short delay
    setTimeout(initializeTranslationApp, 100);
  }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeTranslationApp);
} else {
  initializeTranslationApp();
}

export default TranslationApp;
