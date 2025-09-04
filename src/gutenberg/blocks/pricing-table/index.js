import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

registerBlockType('gst/pricing-table', {
  title: __('Pricing Table', 'get-sheilded-theme'),
  description: __('A pricing table with multiple columns', 'get-sheilded-theme'),
  category: 'get-sheilded',
  icon: 'money-alt',
  supports: {
    align: ['wide', 'full'],
  },

  edit: () => {
    const blockProps = useBlockProps({ className: 'gst-pricing-table' });

    return (
      <div {...blockProps}>
        <InnerBlocks
          allowedBlocks={['gst/pricing-column']}
          template={[
            ['gst/pricing-column'],
            ['gst/pricing-column'],
            ['gst/pricing-column'],
          ]}
        />
      </div>
    );
  },

  save: () => {
    const blockProps = useBlockProps.save({ className: 'gst-pricing-table' });

    return (
      <div {...blockProps}>
        <InnerBlocks.Content />
      </div>
    );
  },
});
