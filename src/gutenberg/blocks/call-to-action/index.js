import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

registerBlockType('gst/call-to-action', {
  title: __('Call to Action', 'get-sheilded-theme'),
  description: __('A call-to-action section with customizable content', 'get-sheilded-theme'),
  category: 'get-sheilded',
  icon: 'megaphone',
  supports: {
    align: ['wide', 'full'],
  },

  edit: () => {
    const blockProps = useBlockProps({ className: 'gst-cta' });

    return (
      <div {...blockProps}>
        <InnerBlocks
          allowedBlocks={['core/heading', 'core/paragraph', 'core/button']}
          template={[
            ['core/heading', {
              placeholder: __('Call to Action Title', 'get-sheilded-theme'),
              level: 2,
            }],
            ['core/paragraph', {
              placeholder: __('Compelling description...', 'get-sheilded-theme'),
            }],
            ['core/button', {
              text: __('Take Action', 'get-sheilded-theme'),
            }],
          ]}
        />
      </div>
    );
  },

  save: () => {
    const blockProps = useBlockProps.save({ className: 'gst-cta' });

    return (
      <div {...blockProps}>
        <InnerBlocks.Content />
      </div>
    );
  },
});
