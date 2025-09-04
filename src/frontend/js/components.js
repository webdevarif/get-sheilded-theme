/**
 * Frontend Components JavaScript with GSAP
 * 
 * @package GetShieldedTheme
 */

import { GSAPAnimations } from '../../lib/gsap-animations';

class GSTComponents {
    constructor() {
        this.components = new Map();
    }

    init() {
        this.initFeatureCards();
        this.initTestimonials();
        this.initPricingTables();
        this.initCallToAction();
        console.log('GST Components initialized with GSAP');
    }

    initFeatureCards() {
        const featureCards = document.querySelectorAll('.gst-feature-card');
        
        featureCards.forEach(card => {
            // Add GSAP classes for animations
            card.classList.add('gsap-hover-lift', 'gsap-scroll-trigger');

            // Initialize icons if Lucide is available
            this.initIconsInCard(card);
        });

        this.components.set('featureCards', featureCards);
    }

    initIconsInCard(card) {
        const iconElement = card.querySelector('.lucide');
        if (iconElement && typeof lucide !== 'undefined') {
            const iconName = iconElement.getAttribute('data-icon');
            if (iconName && lucide[iconName]) {
                iconElement.innerHTML = lucide[iconName]();
            }
        }
    }

    initTestimonials() {
        const testimonials = document.querySelectorAll('.gst-testimonial');
        
        testimonials.forEach(testimonial => {
            // Add GSAP classes for animations
            testimonial.classList.add('gsap-scroll-trigger');
            
            // Add quote hover animation
            const quote = testimonial.querySelector('.gst-testimonial__quote');
            if (quote) {
                quote.classList.add('gsap-hover-scale');
            }
        });

        this.components.set('testimonials', testimonials);
    }

    initPricingTables() {
        const pricingTables = document.querySelectorAll('.gst-pricing-table');
        
        pricingTables.forEach(table => {
            // Make table a stagger container
            table.classList.add('gsap-stagger-container');
            
            const columns = table.querySelectorAll('.gst-pricing-column');
            
            columns.forEach((column) => {
                // Add GSAP classes for stagger animation and hover effects
                column.classList.add('gsap-stagger-item', 'gsap-hover-lift');
            });
        });

        this.components.set('pricingTables', pricingTables);
    }

    initCallToAction() {
        const ctaSections = document.querySelectorAll('.gst-cta');
        
        ctaSections.forEach(cta => {
            // Add GSAP scroll trigger
            cta.classList.add('gsap-scroll-trigger');
            
            // Enhance buttons in CTA with GSAP hover effects
            const buttons = cta.querySelectorAll('.wp-block-button__link');
            buttons.forEach(button => {
                button.classList.add('gsap-hover-bounce');
            });
        });

        this.components.set('ctaSections', ctaSections);
    }

    // Utility method to refresh components
    refresh() {
        this.init();
    }

    // Get component instances
    getComponent(name) {
        return this.components.get(name);
    }

    // Add custom component
    addComponent(name, element, initFunction) {
        if (typeof initFunction === 'function') {
            initFunction(element);
        }
        this.components.set(name, element);
    }
}

// Initialize components
const gstComponents = new GSTComponents();

// Make it globally available
window.gstComponents = gstComponents;
