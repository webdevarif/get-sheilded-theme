// React Admin Component using reusable components
import { render } from '@wordpress/element';
import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import CustomSelect from './components/CustomSelect';
import CustomInput from './components/CustomInput';
import FormField from './components/FormField';

const TemplateSettings = () => {
    const [templateType, setTemplateType] = useState('');
    const [displayOption, setDisplayOption] = useState('');
    const [selectedPages, setSelectedPages] = useState([]);
    const [excludePages, setExcludePages] = useState([]);
    const [priority, setPriority] = useState(10);
    const [pages, setPages] = useState([]);

    // Load pages from WordPress
    useEffect(() => {
        // Simple fetch to get pages
        fetch('/wp-json/wp/v2/pages?per_page=100&status=publish')
            .then(response => response.json())
            .then(data => {
                const pageOptions = data.map(page => ({
                    label: page.title.rendered,
                    value: page.id.toString()
                }));
                setPages(pageOptions);
            })
            .catch(error => console.error('Error loading pages:', error));
    }, []);

    // Load existing values
    useEffect(() => {
        const templateTypeField = document.querySelector('#gst_template_type');
        const displayOptionField = document.querySelector('#gst_display_option');
        const selectedPagesField = document.querySelector('#gst_selected_pages');
        const excludePagesField = document.querySelector('#gst_exclude_pages');
        const priorityField = document.querySelector('#gst_priority');

        if (templateTypeField) setTemplateType(templateTypeField.value);
        if (displayOptionField) setDisplayOption(displayOptionField.value);
        if (priorityField) setPriority(parseInt(priorityField.value) || 10);
        
        if (selectedPagesField) {
            try {
                const selected = JSON.parse(selectedPagesField.value || '[]');
                setSelectedPages(selected);
            } catch (e) {
                setSelectedPages([]);
            }
        }
        
        if (excludePagesField) {
            try {
                const excluded = JSON.parse(excludePagesField.value || '[]');
                setExcludePages(excluded);
            } catch (e) {
                setExcludePages([]);
            }
        }
    }, []);

    // Update hidden fields when React state changes
    useEffect(() => {
        const templateTypeField = document.querySelector('#gst_template_type');
        const displayOptionField = document.querySelector('#gst_display_option');
        const selectedPagesField = document.querySelector('#gst_selected_pages');
        const excludePagesField = document.querySelector('#gst_exclude_pages');
        const priorityField = document.querySelector('#gst_priority');

        if (templateTypeField) templateTypeField.value = templateType;
        if (displayOptionField) displayOptionField.value = displayOption;
        if (priorityField) priorityField.value = priority;
        if (selectedPagesField) selectedPagesField.value = JSON.stringify(selectedPages);
        if (excludePagesField) excludePagesField.value = JSON.stringify(excludePages);
    }, [templateType, displayOption, selectedPages, excludePages, priority]);

    const templateTypeOptions = [
        { label: __('Select Type', 'get-sheilded-theme'), value: '' },
        { label: __('Header', 'get-sheilded-theme'), value: 'header' },
        { label: __('Footer', 'get-sheilded-theme'), value: 'footer' }
    ];

    const displayOptions = [
        { label: __('Select Option', 'get-sheilded-theme'), value: '' },
        { label: __('Entire Site', 'get-sheilded-theme'), value: 'entire_site' },
        { label: __('Specific Pages', 'get-sheilded-theme'), value: 'specific_pages' }
    ];

    return (
        <div style={{ padding: '16px 0px' }}>
            <FormField
                label={__('Template Type', 'get-sheilded-theme')}
                help={__('Choose whether this template is for header or footer', 'get-sheilded-theme')}
            >
                <CustomSelect
                    value={templateTypeOptions.find(option => option.value === templateType)}
                    onChange={(option) => setTemplateType(option ? option.value : '')}
                    options={templateTypeOptions}
                    placeholder={__('Select Type', 'get-sheilded-theme')}
                />
            </FormField>

            <FormField
                label={__('Display On', 'get-sheilded-theme')}
                help={__('Choose where this template should be displayed', 'get-sheilded-theme')}
            >
                <CustomSelect
                    value={displayOptions.find(option => option.value === displayOption)}
                    onChange={(option) => setDisplayOption(option ? option.value : '')}
                    options={displayOptions}
                    placeholder={__('Select Option', 'get-sheilded-theme')}
                />
            </FormField>

            {displayOption === 'specific_pages' && (
                <FormField
                    label={__('Selected Pages', 'get-sheilded-theme')}
                    help={__('Search and select specific pages where this template should appear', 'get-sheilded-theme')}
                >
                    <CustomSelect
                        isMulti
                        value={selectedPages}
                        onChange={setSelectedPages}
                        options={pages}
                        placeholder={__('Search and select pages...', 'get-sheilded-theme')}
                    />
                </FormField>
            )}

            {displayOption === 'entire_site' && (
                <FormField
                    label={__('Exclude Pages', 'get-sheilded-theme')}
                    help={__('Search and select pages to exclude from this template', 'get-sheilded-theme')}
                >
                    <CustomSelect
                        isMulti
                        value={excludePages}
                        onChange={setExcludePages}
                        options={pages}
                        placeholder={__('Search and select pages to exclude...', 'get-sheilded-theme')}
                    />
                </FormField>
            )}

            <FormField
                label={__('Priority', 'get-sheilded-theme')}
                help={__('Higher numbers = higher priority (1-100)', 'get-sheilded-theme')}
            >
                <CustomInput
                    type="number"
                    value={priority}
                    onChange={(e) => setPriority(parseInt(e.target.value) || 10)}
                    min="1"
                    max="100"
                />
            </FormField>
        </div>
    );
};

// Initialize React component when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('gst-template-settings-react');
    if (container) {
        render(<TemplateSettings />, container);
    }
});

export default TemplateSettings;
