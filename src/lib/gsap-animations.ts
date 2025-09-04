import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

// Register ScrollTrigger plugin
gsap.registerPlugin(ScrollTrigger);

/**
 * GSAP Animation Utilities
 */
export class GSAPAnimations {
  private static tl: gsap.core.Timeline;

  /**
   * Initialize GSAP animations
   */
  static init(): void {
    this.setupScrollTriggers();
    this.setupHoverAnimations();
    this.initPageLoad();
  }

  /**
   * Fade in up animation
   */
  static fadeInUp(element: string | Element, options: gsap.TweenVars = {}): gsap.core.Tween {
    return gsap.fromTo(
      element,
      {
        opacity: 0,
        y: 30,
      },
      {
        opacity: 1,
        y: 0,
        duration: 0.6,
        ease: 'power2.out',
        ...options,
      }
    );
  }

  /**
   * Fade in animation
   */
  static fadeIn(element: string | Element, options: gsap.TweenVars = {}): gsap.core.Tween {
    return gsap.fromTo(
      element,
      {
        opacity: 0,
      },
      {
        opacity: 1,
        duration: 0.6,
        ease: 'power2.out',
        ...options,
      }
    );
  }

  /**
   * Slide in from left
   */
  static slideInLeft(element: string | Element, options: gsap.TweenVars = {}): gsap.core.Tween {
    return gsap.fromTo(
      element,
      {
        opacity: 0,
        x: -30,
      },
      {
        opacity: 1,
        x: 0,
        duration: 0.6,
        ease: 'power2.out',
        ...options,
      }
    );
  }

  /**
   * Slide in from right
   */
  static slideInRight(element: string | Element, options: gsap.TweenVars = {}): gsap.core.Tween {
    return gsap.fromTo(
      element,
      {
        opacity: 0,
        x: 30,
      },
      {
        opacity: 1,
        x: 0,
        duration: 0.6,
        ease: 'power2.out',
        ...options,
      }
    );
  }

  /**
   * Scale in animation
   */
  static scaleIn(element: string | Element, options: gsap.TweenVars = {}): gsap.core.Tween {
    return gsap.fromTo(
      element,
      {
        opacity: 0,
        scale: 0.8,
      },
      {
        opacity: 1,
        scale: 1,
        duration: 0.6,
        ease: 'back.out(1.7)',
        ...options,
      }
    );
  }

  /**
   * Rotate in animation
   */
  static rotateIn(element: string | Element, options: gsap.TweenVars = {}): gsap.core.Tween {
    return gsap.fromTo(
      element,
      {
        opacity: 0,
        rotation: -10,
        scale: 0.9,
      },
      {
        opacity: 1,
        rotation: 0,
        scale: 1,
        duration: 0.8,
        ease: 'back.out(1.7)',
        ...options,
      }
    );
  }

  /**
   * Stagger animation for multiple elements
   */
  static staggerIn(elements: string | Element[], options: gsap.TweenVars = {}): gsap.core.Timeline {
    const tl = gsap.timeline();
    
    tl.fromTo(
      elements,
      {
        opacity: 0,
        y: 20,
      },
      {
        opacity: 1,
        y: 0,
        duration: 0.6,
        ease: 'power2.out',
        stagger: 0.1,
        ...options,
      }
    );

    return tl;
  }

  /**
   * Hero section entrance animation
   */
  static heroEntrance(): gsap.core.Timeline {
    const tl = gsap.timeline();

    tl.fromTo(
      '.gsap-hero-title',
      {
        opacity: 0,
        y: 50,
        scale: 0.9,
      },
      {
        opacity: 1,
        y: 0,
        scale: 1,
        duration: 1,
        ease: 'power3.out',
      }
    )
      .fromTo(
        '.gsap-hero-subtitle',
        {
          opacity: 0,
          y: 30,
        },
        {
          opacity: 1,
          y: 0,
          duration: 0.8,
          ease: 'power2.out',
        },
        '-=0.5'
      )
      .fromTo(
        '.gsap-hero-button',
        {
          opacity: 0,
          y: 20,
          scale: 0.9,
        },
        {
          opacity: 1,
          y: 0,
          scale: 1,
          duration: 0.6,
          ease: 'back.out(1.7)',
        },
        '-=0.3'
      );

    return tl;
  }

  /**
   * Setup scroll-triggered animations
   */
  private static setupScrollTriggers(): void {
    // Animate elements on scroll
    gsap.utils.toArray('.gsap-scroll-trigger').forEach((element: any) => {
      gsap.fromTo(
        element,
        {
          opacity: 0,
          y: 50,
        },
        {
          opacity: 1,
          y: 0,
          duration: 0.8,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: element,
            start: 'top 80%',
            end: 'bottom 20%',
            toggleActions: 'play none none reverse',
          },
        }
      );
    });

    // Stagger animations for containers
    gsap.utils.toArray('.gsap-stagger-container').forEach((container: any) => {
      const items = container.querySelectorAll('.gsap-stagger-item');
      
      gsap.fromTo(
        items,
        {
          opacity: 0,
          y: 20,
        },
        {
          opacity: 1,
          y: 0,
          duration: 0.6,
          ease: 'power2.out',
          stagger: 0.1,
          scrollTrigger: {
            trigger: container,
            start: 'top 80%',
            end: 'bottom 20%',
            toggleActions: 'play none none reverse',
          },
        }
      );
    });
  }

  /**
   * Setup hover animations
   */
  private static setupHoverAnimations(): void {
    // Hover lift effect
    gsap.utils.toArray('.gsap-hover-lift').forEach((element: any) => {
      element.addEventListener('mouseenter', () => {
        gsap.to(element, {
          y: -4,
          boxShadow: '0 10px 25px rgba(0, 0, 0, 0.15)',
          duration: 0.3,
          ease: 'power2.out',
        });
      });

      element.addEventListener('mouseleave', () => {
        gsap.to(element, {
          y: 0,
          boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)',
          duration: 0.3,
          ease: 'power2.out',
        });
      });
    });

    // Hover scale effect
    gsap.utils.toArray('.gsap-hover-scale').forEach((element: any) => {
      element.addEventListener('mouseenter', () => {
        gsap.to(element, {
          scale: 1.05,
          duration: 0.3,
          ease: 'power2.out',
        });
      });

      element.addEventListener('mouseleave', () => {
        gsap.to(element, {
          scale: 1,
          duration: 0.3,
          ease: 'power2.out',
        });
      });
    });

    // Hover bounce effect
    gsap.utils.toArray('.gsap-hover-bounce').forEach((element: any) => {
      element.addEventListener('mouseenter', () => {
        gsap.to(element, {
          y: -2,
          duration: 0.2,
          ease: 'power2.out',
          yoyo: true,
          repeat: 1,
        });
      });
    });
  }

  /**
   * Page load animations
   */
  private static initPageLoad(): void {
    // Animate elements with specific classes
    gsap.utils.toArray('.gsap-fade-in-up').forEach((element: any) => {
      this.fadeInUp(element);
    });

    gsap.utils.toArray('.gsap-fade-in').forEach((element: any) => {
      this.fadeIn(element);
    });

    gsap.utils.toArray('.gsap-slide-in-left').forEach((element: any) => {
      this.slideInLeft(element);
    });

    gsap.utils.toArray('.gsap-slide-in-right').forEach((element: any) => {
      this.slideInRight(element);
    });

    gsap.utils.toArray('.gsap-scale-in').forEach((element: any) => {
      this.scaleIn(element);
    });

    gsap.utils.toArray('.gsap-rotate-in').forEach((element: any) => {
      this.rotateIn(element);
    });
  }

  /**
   * Refresh ScrollTrigger (useful after content changes)
   */
  static refresh(): void {
    ScrollTrigger.refresh();
  }

  /**
   * Kill all animations
   */
  static killAll(): void {
    gsap.killTweensOf('*');
    ScrollTrigger.killAll();
  }
}

// Export individual functions for convenience
export const {
  fadeInUp,
  fadeIn,
  slideInLeft,
  slideInRight,
  scaleIn,
  rotateIn,
  staggerIn,
  heroEntrance,
} = GSAPAnimations;
