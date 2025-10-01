/**
 * Simple Translation Editor (Vanilla JS)
 * 
 * @package GetsheildedTheme
 * @since 1.0.0
 */

(function($) {
    'use strict';

    let currentElement = null;
    let currentLanguage = '';
    let availableLanguages = {};
    let defaultLanguage = '';

    // Initialize editor
    $(document).ready(function() {
        console.log('Simple translation editor initializing...');
        initEditor();
    });

    function initEditor() {
        // Check if gstTranslate is available
        if (typeof gstTranslate === 'undefined') {
            console.log('gstTranslate not available - waiting for it to load...');
            setTimeout(function() {
                if (typeof gstTranslate !== 'undefined') {
                    initEditor();
                } else {
                    console.log('gstTranslate still not available after timeout');
                    // Use fallback data
                    useFallbackData();
                }
            }, 1000);
            return;
        }
        
        console.log('gstTranslate loaded:', gstTranslate);
        
        currentLanguage = gstTranslate.current_language || 'bn';
        availableLanguages = gstTranslate.available_languages || {};
        defaultLanguage = gstTranslate.default_language || 'en';
        
        // Show sidebar immediately
        showTranslationSidebar();
        makeElementsClickable();
    }
    
    function useFallbackData() {
        console.log('Using fallback data...');
        currentLanguage = 'bn';
        availableLanguages = {
            'en': { name: 'English', flag: '🇺🇸', active: true },
            'bn': { name: 'Bengali', flag: '🇧🇩', active: true },
            'es': { name: 'Spanish', flag: '🇪🇸', active: true }
        };
        defaultLanguage = 'en';
        
        showTranslationSidebar();
        makeElementsClickable();
    }
    
    function makeElementsClickable() {
        // Clean up any invalid elements first
        $('.gst-translatable').each(function() {
            const $el = $(this);
            if (!$el.length || !$el[0] || !$el[0].parentNode) {
                console.log('Removing invalid gst-translatable element');
                $el.remove();
            }
        });
        
        // Make translatable elements clickable
        $('.gst-translatable').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Safety check
            if (!$(this).length || !$(this)[0]) {
                console.log('Invalid element clicked');
                return;
            }
            
            const originalText = $(this).data('original') || $(this).text();
            const context = $(this).data('context') || 'content';
            
            console.log('Text clicked:', originalText);
            
            // Update sidebar with selected text
            $('#gst-original-text').val(originalText);
            $('#gst-translation-text').val('');
            
            // Store current element reference
            currentElement = $(this);
            
            // Load existing translation
            loadExistingTranslation(originalText, context);
        });

        // Add hover effects
        $('.gst-translatable').hover(
            function() {
                if ($(this).length && $(this)[0]) {
                    $(this).addClass('hover');
                }
            },
            function() {
                if ($(this).length && $(this)[0]) {
                    $(this).removeClass('hover');
                }
            }
        );
    }
    
    // Show translation sidebar
    function showTranslationSidebar() {
        console.log('Showing translation sidebar...');
        
        // Get current URL for iframe
        const currentUrl = window.location.href;
        let iframeUrl = currentUrl.replace('?edit_trans=1', '');
        
        // Add translation mode and iframe parameter to iframe URL
        if (iframeUrl.indexOf('?') === -1) {
            iframeUrl += '?edit_trans=1&iframe=1';
        } else {
            iframeUrl += '&edit_trans=1&iframe=1';
        }
        
        // Create layout HTML with sidebar and iframe
        const layoutHtml = `
            <div class="gst-translation-layout active" id="gst-translation-layout">
                <div class="gst-translation-sidebar">
                    <div class="gst-translation-sidebar-content">
                        <div class="gst-translation-sidebar-header">
                            <h3 class="gst-translation-sidebar-title">Translation Editor</h3>
                            <button class="gst-translation-sidebar-close" id="gst-sidebar-close">×</button>
                        </div>
                        
                        <div class="gst-translation-sidebar-body">
                            <h4>Click on any text to translate it</h4>
                            
                            <div class="gst-translation-form">
                                <div id="gst-other-translation-sections" style="margin-top:10px"></div>
                                
                                <div class="language-section">
                                    <h5>
                                        <span class="language-flag" id="gst-from-flag">🌐</span>
                                        <span id="gst-from-label">From</span>
                                    </h5>
                                    <input type="text" id="gst-original-text" value="Select text to translate..." readonly />
                                    <small>Text</small>
                                </div>
                                
                                <div class="language-section">
                                    <h5>
                                        <span class="language-flag" id="gst-to-flag">🌐</span>
                                        <span id="gst-to-label">To</span>
                                    </h5>
                                    <textarea id="gst-translation-text" placeholder="Enter translation..."></textarea>
                                    <small>Text</small>
                                </div>
                                
                                <button type="button" class="gst-auto-translate" id="gst-auto-translate">
                                    Auto Translate
                                </button>
                            </div>
                        </div>
                        
                        <div class="gst-translation-sidebar-footer">
                            <div class="gst-translation-actions">
                                <button type="button" class="button button-secondary" id="gst-cancel-translation">
                                    Cancel
                                </button>
                                <button type="button" class="button button-primary" id="gst-save-translation">
                                    Save Translation
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="gst-website-iframe">
                    <iframe id="gst-website-iframe" src="${iframeUrl}"></iframe>
                </div>
            </div>
        `;

        // Add layout to page
        $('body').append(layoutHtml);
        
        // Bind events
        bindSidebarEvents();
        renderOtherLanguages();
        updateLanguageLabels();
        updateSourceLabel();
        
        console.log('Translation sidebar created');
    }
    
    // Bind sidebar events
    function bindSidebarEvents() {
        // Close button
        $('#gst-sidebar-close').on('click', function() {
            closeSidebar();
        });

        // Close sidebar on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });

        // Cancel button
        $('#gst-cancel-translation').on('click', function() {
            closeSidebar();
        });

        // Save translation
        $('#gst-save-translation').on('click', function() {
            saveTranslation();
        });

        // Auto translate
        $('#gst-auto-translate').on('click', function() {
            autoTranslate();
        });
    }
    
    // Close sidebar
    function closeSidebar() {
        $('#gst-translation-layout').remove();
        currentElement = null;
    }
    
    function loadExistingTranslation(originalText, context) {
        console.log('Loading existing translation for:', originalText);
        // For now, just show the text
        $('#gst-translation-text').val('');
    }

    function updateLanguageLabels() {
        const code = currentLanguage;
        const lang = availableLanguages[code] || { name: code, flag: '🌐' };
        $('#gst-to-label').text('To ' + (lang.name || code));
        $('#gst-to-flag').text(lang.flag || '🌐');
        updateSourceLabel();
    }

    function renderOtherLanguages() {
        const code = currentLanguage;
        let sections = '';
        
        // Show all active languages except the current one and default
        for (const [c, l] of Object.entries(availableLanguages)) {
            if (l.active && c !== code && c !== defaultLanguage) {
                sections += `
                    <div class="gst-lang-repeater" data-code="${c}" style="margin-top:12px; border-top:1px solid #eee; padding-top:8px;">
                        <h5 style="margin:0 0 6px;">${l.flag} ${l.name}</h5>
                        <textarea class="gst-translation-text-extra" data-code="${c}" placeholder="Enter translation..." style="width:100%; min-height:70px;"></textarea>
                    </div>`;
            }
        }
        
        $('#gst-other-translation-sections').html(sections);
    }

    function updateSourceLabel() {
        // Source is defaultLanguage if defined
        let srcCode = defaultLanguage && availableLanguages[defaultLanguage] ? defaultLanguage : null;
        if (!srcCode) {
            // try to infer 'en' or first active as fallback
            if (availableLanguages['en']) srcCode = 'en';
            else srcCode = Object.keys(availableLanguages)[0];
        }
        const src = availableLanguages[srcCode] || { name: 'Source', flag: '🌐' };
        $('#gst-from-label').text('From ' + (src.name || 'Source'));
        $('#gst-from-flag').text(src.flag || '🌐');
    }

    function saveTranslation() {
        const originalText = $('#gst-original-text').val();
        const translation = $('#gst-translation-text').val();
        const language = currentLanguage;
        const context = currentElement ? currentElement.data('context') || 'content' : 'content';

        if (!translation.trim()) {
            alert('Please enter a translation.');
            return;
        }

        console.log('Saving translation:', { originalText, translation, language, context });
        
        // Show loading
        $('#gst-save-translation').html('<span class="gst-loading"></span> Saving...');
        $('#gst-save-translation').prop('disabled', true);

        // For now, just show success
        setTimeout(function() {
            $('#gst-save-translation').html('Save Translation');
            $('#gst-save-translation').prop('disabled', false);
            showNotification('Translation saved successfully!', 'success');
        }, 1000);
    }

    function autoTranslate() {
        const originalText = $('#gst-original-text').val();
        const targetLanguage = currentLanguage;

        if (!originalText.trim()) {
            alert('No text to translate.');
            return;
        }

        console.log('Auto translating:', { originalText, targetLanguage });
        
        // Show loading
        $('#gst-auto-translate').html('<span class="gst-loading"></span> Translating...');
        $('#gst-auto-translate').prop('disabled', true);

        // For now, just show a placeholder
        setTimeout(function() {
            $('#gst-translation-text').val('[Auto-translated: ' + originalText + ']');
            $('#gst-auto-translate').html('Auto Translate');
            $('#gst-auto-translate').prop('disabled', false);
            showNotification('Translation generated!', 'success');
        }, 1000);
    }

    function showNotification(message, type) {
        const notification = $(`
            <div class="gst-notification gst-notification-${type}" style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#28a745' : '#dc3545'};
                color: white;
                padding: 10px 20px;
                border-radius: 4px;
                z-index: 10001;
                font-size: 14px;
            ">
                ${message}
            </div>
        `);

        $('body').append(notification);

        setTimeout(function() {
            notification.fadeOut(function() {
                notification.remove();
            });
        }, 3000);
    }

})(jQuery);
