/**
 * Frontend Main JavaScript with GSAP
 * 
 * @package GetsheildedTheme
 */

import '../scss/main.scss';
import { GSAPAnimations } from '../../lib/gsap-animations';
import Header1Block from '../../gutenberg/blocks/header-1/frontend';

class GetsheildedTheme {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.initComponents();
        this.initGSAP();
        console.log('Get sheilded Theme initialized with GSAP');
    }

    initGSAP() {
        // Initialize GSAP animations
        GSAPAnimations.init();
    }

    bindEvents() {
        document.addEventListener('DOMContentLoaded', () => {
            this.onDOMContentLoaded();
        });

        window.addEventListener('load', () => {
            this.onWindowLoad();
        });

        window.addEventListener('resize', () => {
            this.onWindowResize();
        });
    }

    onDOMContentLoaded() {
        // Initialize smooth scrolling
        this.initSmoothScrolling();
        
        // Initialize mobile menu
        this.initMobileMenu();
        
        // Initialize scroll effects
        this.initScrollEffects();
    }

    onWindowLoad() {
        // Initialize animations that require full page load
        this.initAnimations();
    }

    onWindowResize() {
        // Handle responsive adjustments
        this.handleResize();
    }

    initComponents() {
        // Initialize theme components
        if (typeof window.gstComponents !== 'undefined') {
            window.gstComponents.init();
        }
    }

    initSmoothScrolling() {
        // Add smooth scrolling to anchor links
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        
        anchorLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const targetId = link.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    initMobileMenu() {
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');
        
        if (mobileMenuToggle && mobileMenu) {
            mobileMenuToggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('active');
                mobileMenuToggle.classList.toggle('active');
            });
        }
    }

    initScrollEffects() {
        // GSAP ScrollTrigger will handle scroll animations
        // No need for Intersection Observer anymore
        console.log('Scroll effects initialized with GSAP ScrollTrigger');
    }

    initAnimations() {
        // GSAP handles all animations now
        // No specific animations needed for header
    }

    handleResize() {
        // Handle responsive behavior
        const width = window.innerWidth;
        
        // Close mobile menu on desktop
        if (width > 768) {
            const mobileMenu = document.querySelector('.mobile-menu');
            if (mobileMenu) {
                mobileMenu.classList.remove('active');
            }
        }
    }

    // Utility methods
    static ajax(action, data = {}, callback = null) {
        if (typeof gstAjax === 'undefined') {
            console.error('WordPress AJAX not available');
            return;
        }

        const formData = new FormData();
        formData.append('action', action);
        formData.append('nonce', gstAjax.nonce);
        
        Object.keys(data).forEach(key => {
            formData.append(key, data[key]);
        });

        fetch(gstAjax.ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (callback && typeof callback === 'function') {
                callback(data);
            }
        })
        .catch(error => {
            console.error('AJAX Error:', error);
        });
    }
}

// Initialize theme
const gstTheme = new GetsheildedTheme();

// Make it globally available
window.gstTheme = gstTheme;
