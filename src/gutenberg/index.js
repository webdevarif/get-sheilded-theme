// Get Shielded Theme - Gutenberg Blocks
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

console.log('🚀 Loading Get Shielded Theme blocks...');

// Register custom block category
import './block-category';
console.log('✅ Custom block category registered');

// Register blocks
import './blocks/header-1';
console.log('✅ Header 1 block registered');

import './blocks/hero-section';
console.log('✅ Hero Section block registered');

import './blocks/feature-card';
console.log('✅ Feature Card block registered');

import './blocks/testimonial';
console.log('✅ Testimonial block registered');

import './blocks/call-to-action';
console.log('✅ Call to Action block registered');

import './blocks/pricing-table';
console.log('✅ Pricing Table block registered');

// Load styles
import './styles/editor.scss';
import './styles/style.scss';

console.log('✅ Get Shielded Theme blocks loaded successfully!');
