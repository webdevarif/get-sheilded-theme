import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';

import './style.scss';
import './editor.scss';

registerBlockType('get-shielded/hero-section', {
  title: __('Hero Section', 'get-shielded-theme'),
  description: __('A modern hero section with customizable content', 'get-shielded-theme'),
  category: 'get-shielded',
  icon: 'cover-image',
  supports: {
    align: ['wide', 'full'],
  },
  attributes: {
    backgroundImage: {
      type: 'string',
      default: '',
    },
    overlayColor: {
      type: 'string',
      default: 'rgba(0, 0, 0, 0.5)',
    },
    showOverlay: {
      type: 'boolean',
      default: true,
    },
    minHeight: {
      type: 'string',
      default: '500px',
    },
  },

  edit: ({ attributes, setAttributes }) => {
    const { backgroundImage, overlayColor, showOverlay, minHeight } = attributes;
    const blockProps = useBlockProps({
      className: 'gst-hero-section',
      style: {
        backgroundImage: backgroundImage ? `url(${backgroundImage})` : undefined,
        minHeight,
      },
    });

    return (
      <>
        <InspectorControls>
          <PanelBody title={__('Hero Settings', 'get-shielded-theme')}>
            <TextControl
              label={__('Background Image URL', 'get-shielded-theme')}
              value={backgroundImage}
              onChange={(value) => setAttributes({ backgroundImage: value })}
            />
            <TextControl
              label={__('Min Height', 'get-shielded-theme')}
              value={minHeight}
              onChange={(value) => setAttributes({ minHeight: value })}
            />
            <ToggleControl
              label={__('Show Overlay', 'get-shielded-theme')}
              checked={showOverlay}
              onChange={(value) => setAttributes({ showOverlay: value })}
            />
            {showOverlay && (
              <TextControl
                label={__('Overlay Color', 'get-shielded-theme')}
                value={overlayColor}
                onChange={(value) => setAttributes({ overlayColor: value })}
              />
            )}
          </PanelBody>
        </InspectorControls>

        <div {...blockProps}>
          {showOverlay && (
            <div
              className="gst-hero-overlay"
              style={{ backgroundColor: overlayColor }}
            />
          )}
          <div className="gst-hero-content">
            <InnerBlocks
              allowedBlocks={['core/heading', 'core/paragraph', 'core/button', 'core/spacer']}
              template={[
                ['core/heading', {
                  placeholder: __('Hero Title', 'get-shielded-theme'),
                  level: 1,
                  className: 'hero-title',
                }],
                ['core/paragraph', {
                  placeholder: __('Hero description goes here...', 'get-shielded-theme'),
                  className: 'hero-description',
                }],
                ['core/button', {
                  text: __('Get Started', 'get-shielded-theme'),
                  className: 'hero-button',
                }],
              ]}
            />
          </div>
        </div>
      </>
    );
  },

  save: ({ attributes }) => {
    const { backgroundImage, overlayColor, showOverlay, minHeight } = attributes;
    const blockProps = useBlockProps.save({
      className: 'gst-hero-section',
      style: {
        backgroundImage: backgroundImage ? `url(${backgroundImage})` : undefined,
        minHeight,
      },
    });

    return (
      <div {...blockProps}>
        {showOverlay && (
          <div
            className="gst-hero-overlay"
            style={{ backgroundColor: overlayColor }}
          />
        )}
        <div className="gst-hero-content">
          <InnerBlocks.Content />
        </div>
      </div>
    );
  },
});
