import { render } from '@wordpress/element';
import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { CustomSelect, CustomInput, FormField } from '../../components';

const TemplateSettings = () => {
    const [templateType, setTemplateType] = useState('');
    const [displayOption, setDisplayOption] = useState('');
    const [selectedPages, setSelectedPages] = useState([]);
    const [excludePages, setExcludePages] = useState([]);
    const [priority, setPriority] = useState(10);
    const [pages, setPages] = useState([]);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        // Fetch pages from WordPress REST API
        const fetchPages = async () => {
            try {
                const response = await fetch('/wp-json/wp/v2/pages?per_page=100&status=publish');
                const data = await response.json();
                const pageOptions = data.map(page => ({
                    value: page.id.toString(),
                    label: page.title.rendered
                }));
                setPages(pageOptions);
            } catch (error) {
                console.error('Error loading pages:', error);
            } finally {
                setIsLoading(false);
            }
        };

        fetchPages();

        // Load initial values from hidden fields
        const templateTypeField = document.getElementById('gst_template_type');
        const displayOptionField = document.getElementById('gst_display_option');
        const selectedPagesField = document.getElementById('gst_selected_pages');
        const excludePagesField = document.getElementById('gst_exclude_pages');
        const priorityField = document.getElementById('gst_priority');

        if (templateTypeField) setTemplateType(templateTypeField.value);
        if (displayOptionField) setDisplayOption(displayOptionField.value);
        if (priorityField) setPriority(parseInt(priorityField.value) || 10);

        if (selectedPagesField && selectedPagesField.value) {
            try {
                const selectedIds = JSON.parse(selectedPagesField.value);
                setSelectedPages(selectedIds);
            } catch (e) {
                setSelectedPages([]);
            }
        }

        if (excludePagesField && excludePagesField.value) {
            try {
                const excludeIds = JSON.parse(excludePagesField.value);
                setExcludePages(excludeIds);
            } catch (e) {
                setExcludePages([]);
            }
        }
    }, []);

    useEffect(() => {
        // Sync React state with hidden fields for form submission
        const templateTypeField = document.getElementById('gst_template_type');
        const displayOptionField = document.getElementById('gst_display_option');
        const selectedPagesField = document.getElementById('gst_selected_pages');
        const excludePagesField = document.getElementById('gst_exclude_pages');
        const priorityField = document.getElementById('gst_priority');

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


    if (isLoading) {
        return <p>{__('Loading...', 'get-sheilded-theme')}</p>;
    }

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
                        value={pages.filter(page => selectedPages.includes(page.value))}
                        onChange={(selectedOptions) => setSelectedPages(selectedOptions ? selectedOptions.map(option => option.value) : [])}
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
                        value={pages.filter(page => excludePages.includes(page.value))}
                        onChange={(selectedOptions) => setExcludePages(selectedOptions ? selectedOptions.map(option => option.value) : [])}
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
    const templateSettingsContainer = document.getElementById('gst-template-settings-react');
    if (templateSettingsContainer) {
        render(<TemplateSettings />, templateSettingsContainer);
    }
});

export default TemplateSettings;
