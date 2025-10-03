import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

registerBlockType('get-shielded/call-to-action', {
  title: __('Call to Action', 'get-shielded-theme'),
  description: __('A call-to-action section with customizable content', 'get-shielded-theme'),
  category: 'get-shielded',
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
              placeholder: __('Call to Action Title', 'get-shielded-theme'),
              level: 2,
            }],
            ['core/paragraph', {
              placeholder: __('Compelling description...', 'get-shielded-theme'),
            }],
            ['core/button', {
              text: __('Take Action', 'get-shielded-theme'),
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
