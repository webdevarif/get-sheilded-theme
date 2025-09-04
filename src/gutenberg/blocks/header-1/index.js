// Header 1 Block - Editor Interface
// This file provides the editor interface for the Header 1 block
// The block itself is registered by PHP in block.php

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

import './style.scss';
import './editor.scss';

// Register the block editor interface
registerBlockType('gst/header-1', {
  title: __('Header 1', 'get-sheilded-theme'),
  description: __('A responsive header with desktop/mobile logo options and smooth animations', 'get-sheilded-theme'),
  category: 'get-sheilded',
  icon: 'admin-site',
  supports: {
    align: ['wide', 'full'],
  },
  // Simple editor interface
  edit: ({ attributes }) => {
    const {
      desktopLogo,
      mobileLogo,
      logoText,
      showLogoText,
      navigationItems,
      ctaText,
      ctaUrl,
      backgroundColor,
      textColor,
      ctaBackgroundColor,
      ctaTextColor,
    } = attributes;

    const blockProps = useBlockProps({
      className: 'gst-header-1-editor',
      style: {
        backgroundColor,
        color: textColor,
      },
    });

    return (
      <div {...blockProps}>
        <div className="bg-black py-1 px-5 text-xs text-gray-400">
          <span className="font-normal tracking-wide">{__('Homepage', 'get-sheilded-theme')}</span>
        </div>
        
        <header className="relative w-full" style={{ backgroundColor }}>
          <div className="flex items-center justify-between px-5 h-[70px] max-w-7xl mx-auto">
            
            <div className="flex-none">
              <div className="flex items-center gap-3">
                <div className="flex-shrink-0 flex items-center justify-center">
                  {desktopLogo ? (
                    <img src={desktopLogo.url} alt={logoText} className="h-10 w-auto" />
                  ) : (
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" className="h-10 w-10">
                      <circle cx="20" cy="20" r="18" fill="url(#logoGradient)" stroke="#84cc16" strokeWidth="2"/>
                      <path d="M12 20L18 26L28 14" stroke="#84cc16" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"/>
                      <defs>
                        <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                          <stop offset="0%" stopColor="#84cc16"/>
                          <stop offset="100%" stopColor="#65a30d"/>
                        </linearGradient>
                      </defs>
                    </svg>
                  )}
                </div>
                {showLogoText && (
                  <span className="text-white font-bold text-lg tracking-wider uppercase">
                    {logoText}
                  </span>
                )}
              </div>
            </div>

            <nav className="flex-1 flex justify-center hidden md:flex">
              <ul className="flex list-none m-0 p-0 gap-8">
                {navigationItems.map((item, index) => (
                  <li key={index} className="relative">
                    <a 
                      href={item.url} 
                      className={`text-white no-underline font-medium text-base transition-colors duration-300 flex items-center gap-1 hover:text-lime-400 ${item.active ? 'text-lime-400' : ''}`}
                    >
                      {item.label}
                      {item.label === 'Services' && (
                        <span className="text-xs text-white transition-transform duration-300 group-hover:rotate-180">▼</span>
                      )}
                    </a>
                  </li>
                ))}
              </ul>
            </nav>

            <div className="flex-none hidden md:flex items-center gap-4">
              {/* Language Switcher */}
              <div className="language-switcher">
                <div className="language-switcher-dropdown">
                  <button className="language-switcher-toggle" aria-expanded="false">
                    <span className="language-flag language-flag-en"></span>
                    <span className="language-name">English</span>
                    <span className="language-arrow">▼</span>
                  </button>
                  
                  <ul className="language-switcher-menu">
                    <li>
                      <a href="#" className="language-link language-link-bn">
                        <span className="language-flag language-flag-bn"></span>
                        <span className="language-name">বাংলা</span>
                      </a>
                    </li>
                    <li>
                      <a href="#" className="language-link language-link-es">
                        <span className="language-flag language-flag-es"></span>
                        <span className="language-name">Español</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>

              <a 
                href={ctaUrl} 
                className="inline-block px-6 py-3 rounded-lg no-underline font-semibold text-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg"
                style={{ 
                  backgroundColor: ctaBackgroundColor, 
                  color: ctaTextColor,
                  boxShadow: '0 4px 12px rgba(132, 204, 22, 0.3)'
                }}
              >
                {ctaText}
              </a>
            </div>

            <button 
              className="md:hidden flex flex-col bg-transparent border-none cursor-pointer p-2 gap-1 hover:opacity-80 transition-opacity duration-300"
              aria-label={__('Toggle mobile menu', 'get-sheilded-theme')}
            >
              <span className="w-6 h-0.5 bg-white rounded-sm transition-all duration-300"></span>
              <span className="w-6 h-0.5 bg-white rounded-sm transition-all duration-300"></span>
              <span className="w-6 h-0.5 bg-white rounded-sm transition-all duration-300"></span>
            </button>
          </div>
        </header>

        <div className="h-px bg-white w-full"></div>
      </div>
    );
  },
  // No save function - PHP handles frontend rendering
  save: () => null,
});