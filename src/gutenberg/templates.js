// Get Shielded Theme - Templates Sidebar Panel
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

const TemplatesSidebarPanel = () => {
    const [selectedPages, setSelectedPages] = useState([]);
    const [excludePages, setExcludePages] = useState([]);
    
    const meta = useSelect((select) => select('core/editor').getEditedPostAttribute('meta'), []);
    const { editPost } = useDispatch('core/editor');
    
    // Get pages for selection
    const pages = useSelect((select) => {
        return select('core').getEntityRecords('postType', 'page') || [];
    }, []);
    
    // Get current post type
    const postType = useSelect((select) => {
        return select('core/editor').getCurrentPostType();
    }, []);
    
    // Parse JSON data from meta
    useEffect(() => {
        if (meta?.gst_selected_pages) {
            try {
                const parsed = JSON.parse(meta.gst_selected_pages);
                setSelectedPages(Array.isArray(parsed) ? parsed : []);
            } catch (e) {
                setSelectedPages([]);
            }
        }
        
        if (meta?.gst_exclude_pages) {
            try {
                const parsed = JSON.parse(meta.gst_exclude_pages);
                setExcludePages(Array.isArray(parsed) ? parsed : []);
            } catch (e) {
                setExcludePages([]);
            }
        }
    }, [meta?.gst_selected_pages, meta?.gst_exclude_pages]);
    
    // Don't show for non-template post types
    if (postType !== 'gst_theme_templates') {
        return null;
    }
    
    const templateType = meta?.gst_template_type || '';
    const displayOption = meta?.gst_display_option || '';
    const templatePriority = meta?.gst_priority || 10;
    
    const updateMeta = (key, value) => {
        editPost({ meta: { ...meta, [key]: value } });
    };
    
    
    return (
        <PluginDocumentSettingPanel
            name="gst-templates-settings-panel"
            title={__("Template Settings", "get-shielded-theme")}
            className="gst-templates-settings-panel"
            initialOpen={true}
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
                    {__("Template Type", "get-shielded-theme")}
                </label>
                <Select
                    value={templateType ? { value: templateType, label: templateType === 'header' ? __('Header', 'get-shielded-theme') : __('Footer', 'get-shielded-theme') } : null}
                    onChange={(selectedOption) => updateMeta('gst_template_type', selectedOption ? selectedOption.value : '')}
                    options={[
                        { value: 'header', label: __('Header', 'get-shielded-theme') },
                        { value: 'footer', label: __('Footer', 'get-shielded-theme') }
                    ]}
                    placeholder={__('Select Type', 'get-shielded-theme')}
                    isClearable
                    styles={{
                        control: (provided, state) => ({
                            ...provided,
                            minHeight: '32px',
                            height: '32px',
                            fontSize: '13px',
                            borderColor: state.isFocused ? '#007cba' : '#8c8f94',
                            boxShadow: state.isFocused ? '0 0 0 1px #007cba' : 'none',
                            '&:hover': {
                                borderColor: '#007cba'
                            }
                        }),
                        valueContainer: (provided) => ({
                            ...provided,
                            height: '32px',
                            padding: '0 8px'
                        }),
                        input: (provided) => ({
                            ...provided,
                            margin: '0px'
                        }),
                        indicatorsContainer: (provided) => ({
                            ...provided,
                            height: '32px'
                        }),
                        menu: (provided) => ({
                            ...provided,
                            fontSize: '13px',
                            zIndex: 999999
                        })
                    }}
                />
            </div>
            
            <div style={{ marginBottom: '16px' }}>
                <label style={{ 
                    display: 'block', 
                    marginBottom: '8px', 
                    fontSize: '11px', 
                    fontWeight: '500', 
                    textTransform: 'uppercase', 
                    color: '#1e1e1e' 
                }}>
                    {__("Display On", "get-shielded-theme")}
                </label>
                <Select
                    value={displayOption ? { 
                        value: displayOption, 
                        label: displayOption === 'entire_site' 
                            ? __('Entire Site', 'get-shielded-theme') 
                            : __('Specific Pages', 'get-shielded-theme') 
                    } : null}
                    onChange={(selectedOption) => updateMeta('gst_display_option', selectedOption ? selectedOption.value : '')}
                    options={[
                        { value: 'entire_site', label: __('Entire Site', 'get-shielded-theme') },
                        { value: 'specific_pages', label: __('Specific Pages', 'get-shielded-theme') }
                    ]}
                    placeholder={__('Select Option', 'get-shielded-theme')}
                    isClearable
                    styles={{
                        control: (provided, state) => ({
                            ...provided,
                            minHeight: '32px',
                            height: '32px',
                            fontSize: '13px',
                            borderColor: state.isFocused ? '#007cba' : '#8c8f94',
                            boxShadow: state.isFocused ? '0 0 0 1px #007cba' : 'none',
                            '&:hover': {
                                borderColor: '#007cba'
                            }
                        }),
                        valueContainer: (provided) => ({
                            ...provided,
                            height: '32px',
                            padding: '0 8px'
                        }),
                        input: (provided) => ({
                            ...provided,
                            margin: '0px'
                        }),
                        indicatorsContainer: (provided) => ({
                            ...provided,
                            height: '32px'
                        }),
                        menu: (provided) => ({
                            ...provided,
                            fontSize: '13px',
                            zIndex: 999999
                        })
                    }}
                />
            </div>
            
            {displayOption === 'specific_pages' && (
                <div style={{ marginBottom: '16px' }}>
                    <label style={{ 
                        display: 'block', 
                        marginBottom: '8px', 
                        fontSize: '11px', 
                        fontWeight: '500', 
                        textTransform: 'uppercase', 
                        color: '#1e1e1e' 
                    }}>
                        {__("Select Pages", "get-shielded-theme")}
                    </label>
                    <Select
                        value={selectedPages}
                        onChange={(selectedOptions) => {
                            const newPages = selectedOptions || [];
                            setSelectedPages(newPages);
                            updateMeta('gst_selected_pages', JSON.stringify(newPages));
                        }}
                        options={pages.map(page => ({
                            value: page.id,
                            label: page.title.rendered
                        }))}
                        placeholder={__('Choose pages...', 'get-shielded-theme')}
                        isMulti
                        isClearable
                        isSearchable
                        styles={{
                            control: (provided, state) => ({
                                ...provided,
                                minHeight: '32px',
                                fontSize: '13px',
                                borderColor: state.isFocused ? '#007cba' : '#8c8f94',
                                boxShadow: state.isFocused ? '0 0 0 1px #007cba' : 'none',
                                '&:hover': {
                                    borderColor: '#007cba'
                                }
                            }),
                            valueContainer: (provided) => ({
                                ...provided,
                                padding: '2px 8px'
                            }),
                            input: (provided) => ({
                                ...provided,
                                margin: '0px'
                            }),
                            indicatorsContainer: (provided) => ({
                                ...provided,
                                height: '32px'
                            }),
                            menu: (provided) => ({
                                ...provided,
                                fontSize: '13px',
                                zIndex: 999999
                            }),
                            multiValue: (provided) => ({
                                ...provided,
                                backgroundColor: '#007cba',
                                borderRadius: '3px'
                            }),
                            multiValueLabel: (provided) => ({
                                ...provided,
                                color: 'white',
                                fontSize: '12px'
                            }),
                            multiValueRemove: (provided) => ({
                                ...provided,
                                color: 'white',
                                '&:hover': {
                                    backgroundColor: '#005a87',
                                    color: 'white'
                                }
                            })
                        }}
                        noOptionsMessage={() => __('No pages found', 'get-shielded-theme')}
                    />
                </div>
            )}
            
            {displayOption === 'entire_site' && (
                <div style={{ marginBottom: '16px' }}>
                    <label style={{ 
                        display: 'block', 
                        marginBottom: '8px', 
                        fontSize: '11px', 
                        fontWeight: '500', 
                        textTransform: 'uppercase', 
                        color: '#1e1e1e' 
                    }}>
                        {__("Exclude Pages", "get-shielded-theme")}
                    </label>
                    <Select
                        value={excludePages}
                        onChange={(selectedOptions) => {
                            const newPages = selectedOptions || [];
                            setExcludePages(newPages);
                            updateMeta('gst_exclude_pages', JSON.stringify(newPages));
                        }}
                        options={pages.map(page => ({
                            value: page.id,
                            label: page.title.rendered
                        }))}
                        placeholder={__('Choose pages to exclude...', 'get-shielded-theme')}
                        isMulti
                        isClearable
                        isSearchable
                        styles={{
                            control: (provided, state) => ({
                                ...provided,
                                minHeight: '32px',
                                fontSize: '13px',
                                borderColor: state.isFocused ? '#007cba' : '#8c8f94',
                                boxShadow: state.isFocused ? '0 0 0 1px #007cba' : 'none',
                                '&:hover': {
                                    borderColor: '#007cba'
                                }
                            }),
                            valueContainer: (provided) => ({
                                ...provided,
                                padding: '2px 8px'
                            }),
                            input: (provided) => ({
                                ...provided,
                                margin: '0px'
                            }),
                            indicatorsContainer: (provided) => ({
                                ...provided,
                                height: '32px'
                            }),
                            menu: (provided) => ({
                                ...provided,
                                fontSize: '13px',
                                zIndex: 999999
                            }),
                            multiValue: (provided) => ({
                                ...provided,
                                backgroundColor: '#dc3545',
                                borderRadius: '3px'
                            }),
                            multiValueLabel: (provided) => ({
                                ...provided,
                                color: 'white',
                                fontSize: '12px'
                            }),
                            multiValueRemove: (provided) => ({
                                ...provided,
                                color: 'white',
                                '&:hover': {
                                    backgroundColor: '#c82333',
                                    color: 'white'
                                }
                            })
                        }}
                        noOptionsMessage={() => __('No pages found', 'get-shielded-theme')}
                    />
                </div>
            )}
            
            
            <div style={{ marginBottom: '16px' }}>
                <label style={{ 
                    display: 'block', 
                    marginBottom: '8px', 
                    fontSize: '11px', 
                    fontWeight: '500', 
                    textTransform: 'uppercase', 
                    color: '#1e1e1e' 
                }}>
                    {__("Priority", "get-shielded-theme")}
                </label>
                <input
                    type="number"
                    min="1"
                    max="100"
                    value={templatePriority}
                    onChange={(e) => updateMeta('gst_priority', parseInt(e.target.value) || 10)}
                    style={{
                        width: '100%',
                        height: '32px',
                        padding: '0 8px',
                        fontSize: '13px',
                        border: '1px solid #8c8f94',
                        borderRadius: '4px',
                        outline: 'none',
                        transition: 'border-color 0.2s, box-shadow 0.2s'
                    }}
                    onFocus={(e) => {
                        e.target.style.borderColor = '#007cba';
                        e.target.style.boxShadow = '0 0 0 1px #007cba';
                    }}
                    onBlur={(e) => {
                        e.target.style.borderColor = '#8c8f94';
                        e.target.style.boxShadow = 'none';
                    }}
                />
                <p style={{ 
                    fontSize: '11px', 
                    color: '#757575', 
                    margin: '4px 0 0 0',
                    fontStyle: 'italic'
                }}>
                    {__('Lower numbers = higher priority', 'get-shielded-theme')}
                </p>
            </div>
            
            {templateType && displayOption && (
                <Notice status="success" isDismissible={false}>
                    <p style={{ margin: 0, fontSize: '12px' }}>
                        {__('Template configured successfully!', 'get-shielded-theme')}
                    </p>
                </Notice>
            )}
            
            {(!templateType || !displayOption) && (
                <Notice status="warning" isDismissible={false}>
                    <p style={{ margin: 0, fontSize: '12px' }}>
                        {__('Please configure template type and display options.', 'get-shielded-theme')}
                    </p>
                </Notice>
            )}
        </PluginDocumentSettingPanel>
    );
};

// Only register if not already registered
if (!wp.plugins.getPlugin('gst-templates-settings-panel')) {
    registerPlugin("gst-templates-settings-panel", { 
        render: TemplatesSidebarPanel,
        icon: 'admin-settings' 
    });
}
