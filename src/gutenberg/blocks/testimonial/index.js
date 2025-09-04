import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';

registerBlockType('gst/testimonial', {
  title: __('Testimonial', 'get-shielded-theme'),
  description: __('A testimonial block with quote, author, and company', 'get-shielded-theme'),
  category: 'get-shielded',
  icon: 'format-quote',
  attributes: {
    quote: {
      type: 'string',
      default: 'This product has transformed our business...',
    },
    author: {
      type: 'string',
      default: 'John Doe',
    },
    company: {
      type: 'string',
      default: 'Example Company',
    },
  },

  edit: ({ attributes, setAttributes }) => {
    const { quote, author, company } = attributes;
    const blockProps = useBlockProps({ className: 'gst-testimonial' });

    return (
      <div {...blockProps}>
        <blockquote className="gst-testimonial__quote">
          <RichText
            value={quote}
            onChange={(value) => setAttributes({ quote: value })}
            placeholder={__('Testimonial quote...', 'get-shielded-theme')}
          />
        </blockquote>
        <div className="gst-testimonial__author">
          <RichText
            tagName="cite"
            value={author}
            onChange={(value) => setAttributes({ author: value })}
            placeholder={__('Author name...', 'get-shielded-theme')}
          />
          <RichText
            tagName="span"
            value={company}
            onChange={(value) => setAttributes({ company: value })}
            placeholder={__('Company name...', 'get-shielded-theme')}
          />
        </div>
      </div>
    );
  },

  save: ({ attributes }) => {
    const { quote, author, company } = attributes;
    const blockProps = useBlockProps.save({ className: 'gst-testimonial' });

    return (
      <div {...blockProps}>
        <blockquote className="gst-testimonial__quote">
          <RichText.Content value={quote} />
        </blockquote>
        <div className="gst-testimonial__author">
          <RichText.Content tagName="cite" value={author} />
          <RichText.Content tagName="span" value={company} />
        </div>
      </div>
    );
  },
});
