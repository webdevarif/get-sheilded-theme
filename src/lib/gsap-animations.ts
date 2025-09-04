import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import VanillaTilt from 'vanilla-tilt';

// Register ScrollTrigger plugin
gsap.registerPlugin(ScrollTrigger);

/**
 * GSAP Animation Utilities
 */
export class GSAPAnimations {
  /**
   * Initialize GSAP animations
   */
  static init(): void {
    this.customAnimation();
    this.imageAnimation();
  }

  /**
   * Function to initialize custom animations
   */
  static customAnimation(): void {
    // Select all elements with the class 'gst-fade-animate'
    const fadeContainers = document.querySelectorAll<HTMLElement>(".gst-fade-animate");

    fadeContainers.forEach((container) => {
      let fadeFrom = "bottom",
          onScroll = 1,
          duration = 1.15,
          fadeOffset = 20,
          delay = 0.15,
          ease = "power2.out";

      if (container.getAttribute("data-offset")) {
        fadeOffset = parseInt(container.getAttribute("data-offset") || "50");
      }
      if (container.getAttribute("data-duration")) {
        duration = parseFloat(container.getAttribute("data-duration") || "1.15");
      }
      if (container.getAttribute("data-animate-from")) {
        fadeFrom = container.getAttribute("data-animate-from") || "bottom";
      }
      if (container.getAttribute("data-on-scroll")) {
        onScroll = parseInt(container.getAttribute("data-on-scroll") || "1");
      }
      if (container.getAttribute("data-delay")) {
        delay = parseFloat(container.getAttribute("data-delay") || "0.15");
      }
      if (container.getAttribute("data-ease")) {
        ease = container.getAttribute("data-ease") || "power2.out";
      }

      // Define animation properties with explicit types
      const animationProps: Record<string, unknown> = { // Changed from 'any' to 'unknown' for better type checking
        opacity: 0,
        ease: ease,
        duration: duration,
        delay: delay
      };

      // Modify animation direction based on fadeFrom attribute
      switch (fadeFrom) {
        case "top":
          animationProps.y = -fadeOffset;
          break;
        case "left":
          animationProps.x = -fadeOffset;
          break;
        case "bottom":
          animationProps.y = fadeOffset;
          break;
        case "right":
          animationProps.x = fadeOffset;
          break;
      }

      // Add scroll trigger if specified
      if (onScroll === 1) {
        animationProps.scrollTrigger = {
          trigger: container,
          start: "top 85%"
        };
      }

      // Apply GSAP animation from defined properties
      gsap.from(container, animationProps);
    });

    const btnCursors = document.querySelectorAll('.gst-btn-cursor');

    btnCursors.forEach(btn => {
      // Cast to HTMLElement to ensure `.style` is accessible
      const htmlBtn = btn as HTMLElement;
    
      htmlBtn.addEventListener("mousemove", event => {
        // Calculate the mouse position relative to the top-left corner of the button
        const rect = htmlBtn.getBoundingClientRect();
        const posX = event.clientX - rect.left;
        const posY = event.clientY - rect.top;
        
        // Update CSS custom properties --x and --y on the HTML element
        htmlBtn.style.setProperty('--x', `${posX}px`);
        htmlBtn.style.setProperty('--y', `${posY}px`);
      });
    });
  }

  /**
   * Function to initialize image animation
   */
  static imageAnimation(): void {
    // Reveal animation for .gst-reveal containers
    const revealContainers = document.querySelectorAll<HTMLElement>(".gst-reveal");
    revealContainers.forEach((container) => {
      const image = container.querySelector("img");
      if (!image) return;

      const tl = gsap.timeline({
        scrollTrigger: {
          trigger: container,
          toggleActions: "play none none none",
        },
      });

      tl.set(container, { autoAlpha: 1 });
      tl.from(container, 1.5, { xPercent: -100, ease: "Power2.out" });
      tl.from(image, 1.5, { xPercent: 100, scale: 1.3, delay: -1.5, ease: "Power2.out" });
    });

    // Inview animation for .gst-inview containers
    const inviewContainers = document.querySelectorAll<HTMLElement>(".gst-inview");
    inviewContainers.forEach((container) => {
      const element = container.querySelector(".gst-inview-wrapper");
      const delayAttr = container.getAttribute("delay") || "0"; // Fallback to "0" if delay is null

      if (!element) return;

      gsap.fromTo(
        element,
        { scale: 1.2 },
        {
          scale: 1,
          duration: 2,
          delay: parseFloat(delayAttr), // Parse the delay attribute
          ease: "power2.out",
          scrollTrigger: {
            trigger: container,
            start: "top bottom",
          },
          onStart: () => {
            container.classList.add("animate");
          },
        }
      );
    });

    // Vivacity animation for .gst-vivacity containers
    const vivacityContainers = document.querySelectorAll<HTMLElement>(".gst-vivacity");
    vivacityContainers.forEach((container) => {
      const max = container.getAttribute("data-max") || "5"; // Fallback to "0" if delay is null
      const speed = container.getAttribute("data-speed") || "2800"; // Fallback to "0" if delay is null
      const perspective = container.getAttribute("data-perspective") || "500"; // Fallback to "0" if delay is null
      VanillaTilt.init(container, {
        max: parseInt(max),
        speed: parseInt(speed),
        perspective: parseInt(perspective),
      });
    });

    // Parallax effect for .gst-parallax-anim and .gst-parallax-self containers
    const parallaxContainers = document.querySelectorAll<HTMLElement>(
      ".gst-parallax-anim, .gst-parallax-self"
    );

    parallaxContainers.forEach((container) => {
      const image = container.querySelector("img") || container; // Use the container itself if no image is found
      if (!image) return;

      // Get the yPercent value from the data-ypercent attribute (default to 30 if not provided)
      const yPercentFrom = container.getAttribute("data-from") || "-30"; // Fallback to "30" if data-ypercent is null
      const yPercentTo = container.getAttribute("data-to") || "30"; // Fallback to "30" if data-ypercent is null

      const tl = gsap.timeline({
        scrollTrigger: {
          trigger: container,
          scrub: 0.5,
        },
      });

      tl.from(image, {
        yPercent: parseFloat(yPercentFrom), // Use the custom or default yPercent value
        ease: "none",
      }).to(image, {
        yPercent: parseFloat(yPercentTo), // Use the custom or default yPercent value
        ease: "none",
      });
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
export const { customAnimation, imageAnimation } = GSAPAnimations;
