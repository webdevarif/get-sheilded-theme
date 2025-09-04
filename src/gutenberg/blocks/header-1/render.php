<?php
/**
 * Header 1 Block - PHP Render Template
 * 
 * @package GetsheildedTheme\Blocks
 * @since 1.0.0
 */

// Get block attributes
$desktop_logo = $attributes['desktopLogo'] ?? null;
$mobile_logo = $attributes['mobileLogo'] ?? null;
$logo_text = $attributes['logoText'] ?? 'GET sheilded';
$show_logo_text = $attributes['showLogoText'] ?? true;
$navigation_items = $attributes['navigationItems'] ?? [];
$cta_text = $attributes['ctaText'] ?? 'Get 30min consultation';
$cta_url = $attributes['ctaUrl'] ?? '#contact';
$background_color = $attributes['backgroundColor'] ?? '#1a4d3a';
$text_color = $attributes['textColor'] ?? '#ffffff';
$cta_background_color = $attributes['ctaBackgroundColor'] ?? '#84cc16';
$cta_text_color = $attributes['ctaTextColor'] ?? '#1a4d3a';

// Get desktop logo URL
$desktop_logo_url = '';
if ($desktop_logo && isset($desktop_logo['url'])) {
    $desktop_logo_url = $desktop_logo['url'];
}

// Get mobile logo URL
$mobile_logo_url = '';
if ($mobile_logo && isset($mobile_logo['url'])) {
    $mobile_logo_url = $mobile_logo['url'];
}

// Block wrapper classes
$wrapper_classes = [
    'gst-header-1',
    'relative',
    'w-full',
    'z-50'
];
$wrapper_class = implode(' ', $wrapper_classes);
?>

<div class="<?php echo esc_attr($wrapper_class); ?>" 
     style="background-color: <?php echo esc_attr($background_color); ?>; color: <?php echo esc_attr($text_color); ?>;">
  
  <!-- Header Top Breadcrumb -->
  <div class="bg-black py-1 px-5 text-xs text-gray-400">
    <span class="font-normal tracking-wide"><?php _e('Homepage', 'get-sheilded-theme'); ?></span>
  </div>
  
  <!-- Main Header -->
  <header class="relative w-full" style="background-color: <?php echo esc_attr($background_color); ?>;">
    <div class="flex items-center justify-between px-5 h-[70px] max-w-7xl mx-auto">
      
      <!-- Logo Section -->
      <div class="flex-none">
        <div class="flex items-center gap-3">
          <div class="flex-shrink-0 flex items-center justify-center">
            <?php if ($desktop_logo_url): ?>
              <img src="<?php echo esc_url($desktop_logo_url); ?>" 
                   alt="<?php echo esc_attr($logo_text); ?>" 
                   class="h-10 w-auto">
            <?php else: ?>
              <!-- Default SVG Logo -->
              <svg width="40" height="40" viewBox="0 0 40 40" fill="none" class="h-10 w-10">
                <circle cx="20" cy="20" r="18" fill="url(#logoGradient)" stroke="#84cc16" stroke-width="2"/>
                <path d="M12 20L18 26L28 14" stroke="#84cc16" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                <defs>
                  <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#84cc16"/>
                    <stop offset="100%" stop-color="#65a30d"/>
                  </linearGradient>
                </defs>
              </svg>
            <?php endif; ?>
          </div>
          <?php if ($show_logo_text): ?>
            <span class="text-white font-bold text-lg tracking-wider uppercase">
              <?php echo esc_html($logo_text); ?>
            </span>
          <?php endif; ?>
        </div>
      </div>

      <!-- Desktop Navigation -->
      <nav class="flex-1 flex justify-center hidden md:flex">
        <ul class="flex list-none m-0 p-0 gap-8">
          <?php foreach ($navigation_items as $index => $item): ?>
            <li class="relative">
              <a href="<?php echo esc_url($item['url']); ?>" 
                 class="text-white no-underline font-medium text-base transition-colors duration-300 flex items-center gap-1 hover:text-lime-400 <?php echo $item['active'] ? 'text-lime-400' : ''; ?>">
                <?php echo esc_html($item['label']); ?>
                <?php if ($item['label'] === 'Services'): ?>
                  <span class="text-xs text-white transition-transform duration-300 group-hover:rotate-180">▼</span>
                <?php endif; ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>

      <!-- CTA Section -->
      <div class="flex-none hidden md:block">
        <a href="<?php echo esc_url($cta_url); ?>" 
           class="inline-block px-6 py-3 rounded-lg no-underline font-semibold text-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg"
           style="background-color: <?php echo esc_attr($cta_background_color); ?>; color: <?php echo esc_attr($cta_text_color); ?>; box-shadow: 0 4px 12px rgba(132, 204, 22, 0.3);">
          <?php echo esc_html($cta_text); ?>
        </a>
      </div>

      <!-- Mobile Menu Toggle -->
      <button class="md:hidden flex flex-col bg-transparent border-none cursor-pointer p-2 gap-1 hover:opacity-80 transition-opacity duration-300"
              aria-label="<?php _e('Toggle mobile menu', 'get-sheilded-theme'); ?>"
              aria-expanded="false">
        <span class="w-6 h-0.5 bg-white rounded-sm transition-all duration-300"></span>
        <span class="w-6 h-0.5 bg-white rounded-sm transition-all duration-300"></span>
        <span class="w-6 h-0.5 bg-white rounded-sm transition-all duration-300"></span>
      </button>
    </div>

    <!-- Mobile Menu -->
    <div class="md:hidden absolute top-full left-0 right-0 bg-white shadow-lg z-50 hidden">
      <div class="bg-black px-5 py-4 flex justify-between items-center">
        <div class="flex items-center justify-center">
          <?php if ($mobile_logo_url): ?>
            <img src="<?php echo esc_url($mobile_logo_url); ?>" 
                 alt="<?php echo esc_attr($logo_text); ?>" 
                 class="h-8 w-auto">
          <?php else: ?>
            <!-- Default Mobile SVG Logo -->
            <svg width="32" height="32" viewBox="0 0 40 40" fill="none" class="h-8 w-8">
              <circle cx="20" cy="20" r="18" fill="url(#mobileLogoGradient)" stroke="#84cc16" stroke-width="2"/>
              <path d="M12 20L18 26L28 14" stroke="#84cc16" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
              <defs>
                <linearGradient id="mobileLogoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#84cc16"/>
                  <stop offset="100%" stop-color="#65a30d"/>
                </linearGradient>
              </defs>
            </svg>
          <?php endif; ?>
        </div>
        <button class="bg-transparent border-none text-white text-xl cursor-pointer p-1 leading-none hover:text-lime-400 transition-colors duration-300"
                aria-label="<?php _e('Close mobile menu', 'get-sheilded-theme'); ?>">
          ✕
        </button>
      </div>
      <nav class="py-5">
        <ul class="list-none m-0 p-0">
          <?php foreach ($navigation_items as $index => $item): ?>
            <li class="border-b border-gray-200 last:border-b-0">
              <a href="<?php echo esc_url($item['url']); ?>" 
                 class="flex items-center justify-between px-5 py-4 text-gray-700 no-underline font-medium text-base transition-all duration-300 hover:bg-gray-50 hover:text-gray-900 <?php echo $item['active'] ? 'text-gray-900 bg-green-50' : ''; ?>">
                <?php echo esc_html($item['label']); ?>
                <?php if ($item['label'] === 'Services'): ?>
                  <span class="text-xs text-gray-500">▼</span>
                <?php endif; ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Header Divider -->
  <div class="h-px bg-white w-full"></div>
</div>

<script>
// Mobile menu functionality
document.addEventListener('DOMContentLoaded', function() {
  const mobileMenuToggle = document.querySelector('.gst-header-1 button[aria-label*="Toggle mobile menu"]');
  const mobileMenu = document.querySelector('.gst-header-1 .md\\:hidden.absolute');
  const mobileMenuClose = document.querySelector('.gst-header-1 button[aria-label*="Close mobile menu"]');
  
  if (mobileMenuToggle && mobileMenu) {
    mobileMenuToggle.addEventListener('click', function() {
      const isOpen = mobileMenu.classList.contains('hidden');
      
      if (isOpen) {
        mobileMenu.classList.remove('hidden');
        mobileMenuToggle.setAttribute('aria-expanded', 'true');
      } else {
        mobileMenu.classList.add('hidden');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }
  
  if (mobileMenuClose && mobileMenu) {
    mobileMenuClose.addEventListener('click', function() {
      mobileMenu.classList.add('hidden');
      mobileMenuToggle.setAttribute('aria-expanded', 'false');
    });
  }
  
  // Close menu when clicking outside
  document.addEventListener('click', function(e) {
    if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
      if (!mobileMenu.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
        mobileMenu.classList.add('hidden');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
      }
    }
  });
  
  // Close menu on escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && mobileMenu && !mobileMenu.classList.contains('hidden')) {
      mobileMenu.classList.add('hidden');
      mobileMenuToggle.setAttribute('aria-expanded', 'false');
    }
  });
});
</script>
