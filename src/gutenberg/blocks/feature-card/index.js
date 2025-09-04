import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl } from '@wordpress/components';

import './style.scss';
import './editor.scss';

registerBlockType('gst/feature-card', {
  title: __('Feature Card', 'get-sheilded-theme'),
  description: __('A modern feature card with icon, title, and description', 'get-sheilded-theme'),
  category: 'get-sheilded',
  icon: 'id-alt',
  supports: {
    align: false,
  },
  attributes: {
    iconName: {
      type: 'string',
      default: 'shield',
    },
    title: {
      type: 'string',
      default: 'Feature Title',
    },
    description: {
      type: 'string',
      default: 'Feature description goes here...',
    },
    cardStyle: {
      type: 'string',
      default: 'default',
    },
  },

  edit: ({ attributes, setAttributes }) => {
    const { iconName, title, description, cardStyle } = attributes;
    const blockProps = useBlockProps({
      className: `gst-feature-card gst-feature-card--${cardStyle}`,
    });

    const iconOptions = [
      { label: 'Shield', value: 'shield' },
      { label: 'Star', value: 'star' },
      { label: 'Heart', value: 'heart' },
      { label: 'Zap', value: 'zap' },
      { label: 'Award', value: 'award' },
      { label: 'Check Circle', value: 'check-circle' },
    ];

    const styleOptions = [
      { label: 'Default', value: 'default' },
      { label: 'Outlined', value: 'outlined' },
      { label: 'Filled', value: 'filled' },
    ];

    return (
      <>
        <InspectorControls>
          <PanelBody title={__('Card Settings', 'get-sheilded-theme')}>
            <SelectControl
              label={__('Icon', 'get-sheilded-theme')}
              value={iconName}
              options={iconOptions}
              onChange={(value) => setAttributes({ iconName: value })}
            />
            <SelectControl
              label={__('Card Style', 'get-sheilded-theme')}
              value={cardStyle}
              options={styleOptions}
              onChange={(value) => setAttributes({ cardStyle: value })}
            />
          </PanelBody>
        </InspectorControls>

        <div {...blockProps}>
          <div className="gst-feature-card__icon">
            <span className={`lucide lucide-${iconName}`}>
              {/* Icon placeholder - would be replaced with actual icon */}
              📋
            </span>
          </div>
          <div className="gst-feature-card__content">
            <RichText
              tagName="h3"
              className="gst-feature-card__title"
              value={title}
              onChange={(value) => setAttributes({ title: value })}
              placeholder={__('Feature title...', 'get-sheilded-theme')}
            />
            <RichText
              tagName="p"
              className="gst-feature-card__description"
              value={description}
              onChange={(value) => setAttributes({ description: value })}
              placeholder={__('Feature description...', 'get-sheilded-theme')}
            />
          </div>
        </div>
      </>
    );
  },

  save: ({ attributes }) => {
    const { iconName, title, description, cardStyle } = attributes;
    const blockProps = useBlockProps.save({
      className: `gst-feature-card gst-feature-card--${cardStyle}`,
    });

    return (
      <div {...blockProps}>
        <div className="gst-feature-card__icon">
          <span className={`lucide lucide-${iconName}`} data-icon={iconName}>
            📋
          </span>
        </div>
        <div className="gst-feature-card__content">
          <RichText.Content
            tagName="h3"
            className="gst-feature-card__title"
            value={title}
          />
          <RichText.Content
            tagName="p"
            className="gst-feature-card__description"
            value={description}
          />
        </div>
      </div>
    );
  },
});
