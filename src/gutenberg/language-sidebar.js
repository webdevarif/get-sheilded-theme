// Get sheilded Theme - Language Sidebar Panel
import { registerPlugin } from "@wordpress/plugins";
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { __ } from "@wordpress/i18n";
import { useState, useEffect } from "@wordpress/element";
import { select, useSelect, useDispatch } from '@wordpress/data';
import { 
    Notice 
} from "@wordpress/components";

// Import react-select
import Select from 'react-select';

const LanguageSidebarPanel = () => {
    const [languages, setLanguages] = useState([]);
    const [selectedLanguage, setSelectedLanguage] = useState(null);
    
    const meta = useSelect((select) => select('core/editor').getEditedPostAttribute('meta'), []);
    const { editPost } = useDispatch('core/editor');
    
    // Get current post type
    const postType = useSelect((select) => {
        return select('core/editor').getCurrentPostType();
    }, []);
    
    // Initialize languages from localized data
    useEffect(() => {
        if (window.gstLanguage && window.gstLanguage.languages) {
            const langOptions = [
                {
                    value: '',
                    label: 'Choose Language',
                    flag: '🌐',
                    name: 'Choose Language'
                },
                ...Object.entries(window.gstLanguage.languages).map(([code, lang]) => ({
                    value: code,
                    label: `${lang.flag} ${lang.name}`,
                    flag: lang.flag,
                    name: lang.name
                }))
            ];
            setLanguages(langOptions);
        }
    }, []);
    
    // Set current language from meta
    useEffect(() => {
        if (meta?.gst_language && languages.length > 0) {
            const currentLang = languages.find(lang => lang.value === meta.gst_language);
            setSelectedLanguage(currentLang || null);
        } else if (languages.length > 0) {
            // Always default to "Choose Language" option for new posts
            const chooseLang = languages.find(lang => lang.value === '');
            setSelectedLanguage(chooseLang || null);
        }
    }, [meta?.gst_language, languages]);
    
    // Handle language change
    const handleLanguageChange = (selectedOption) => {
        setSelectedLanguage(selectedOption);
        editPost({ meta: { gst_language: selectedOption ? selectedOption.value : '' } });
    };
    
    // Don't show panel if no languages or only "Choose Language" option
    if (languages.length === 0 || languages.length <= 1) {
        return null;
    }
    
    // Don't show for templates
    if (postType === 'gst_theme_templates') {
        return null;
    }
    
    return (
        <PluginDocumentSettingPanel
            name="gst-language-panel"
            title={__('Language', 'get-sheilded-theme')}
            className="gst-language-panel"
        >
            <div style={{ marginBottom: '16px' }}>
                <label style={{ 
                    display: 'block', 
                    marginBottom: '8px', 
                    fontSize: '11px', 
                    fontWeight: '500', 
                    textTransform: 'uppercase', 
                    color: '#1e1e1e' 
                }}>
                    {__('Select Language', 'get-sheilded-theme')}
                </label>
                <Select
                    value={selectedLanguage}
                    onChange={handleLanguageChange}
                    options={languages}
                    placeholder={__('Select a language for this content', 'get-sheilded-theme')}
                    isClearable
                    styles={{
                        control: (provided, state) => ({
                            ...provided,
                            minHeight: '32px',
                            height: '32px',
                            fontSize: '13px',
                            borderColor: state.isFocused ? '#007cba' : '#8c8f94',
                            boxShadow: state.isFocused ? '0 0 0 1px #007cba' : 'none',
                        }),
                        valueContainer: (provided) => ({
                            ...provided,
                            height: '30px',
                            padding: '0 8px',
                        }),
                        input: (provided) => ({
                            ...provided,
                            margin: '0px',
                        }),
                        indicatorSeparator: () => ({
                            display: 'none',
                        }),
                        indicatorsContainer: (provided) => ({
                            ...provided,
                            height: '30px',
                        }),
                        dropdownIndicator: (provided) => ({
                            ...provided,
                            padding: '4px 8px',
                        }),
                        clearIndicator: (provided) => ({
                            ...provided,
                            padding: '4px 8px',
                        }),
                        option: (provided, state) => ({
                            ...provided,
                            fontSize: '13px',
                            backgroundColor: state.isSelected ? '#007cba' : state.isFocused ? '#f0f6fc' : 'white',
                            color: state.isSelected ? 'white' : '#1e1e1e',
                            padding: '8px 12px',
                        }),
                        singleValue: (provided) => ({
                            ...provided,
                            color: '#1e1e1e',
                        }),
                        placeholder: (provided) => ({
                            ...provided,
                            color: '#8c8f94',
                        }),
                    }}
                />
                <p style={{ 
                    marginTop: '8px', 
                    fontSize: '12px', 
                    color: '#646970',
                    fontStyle: 'italic'
                }}>
                    {__('Choose the language for this content.', 'get-sheilded-theme')}
                </p>
            </div>
        </PluginDocumentSettingPanel>
    );
};

// Register the plugin
registerPlugin("gst-language-sidebar-panel", {
    render: LanguageSidebarPanel,
    icon: 'translation',
});
