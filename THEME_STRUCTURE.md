# Get Sheilded Theme - Organized Structure

## Overview
This theme follows the Aquila pattern with a well-organized, modular architecture that makes it easy to add, remove, or modify features.

## Directory Structure

```
inc/
├── admin/                    # Admin-related classes
│   └── class-menu-manager.php
├── classes/                  # All theme classes (main + features)
│   ├── class-get-sheilded-theme.php    # Main theme class
│   ├── class-block-registry.php       # Gutenberg block registry
│   ├── class-colorpalette.php         # Color palette feature
│   ├── class-frontendscripts.php      # Frontend scripts feature
│   ├── class-gutenbergblocks.php      # Gutenberg blocks feature
│   ├── class-languages.php            # Languages feature
│   ├── class-layoutsettings.php       # Layout settings feature
│   ├── class-settings.php             # Settings feature
│   └── class-templates.php            # Templates feature
├── helpers/                  # Helper functions and utilities
│   ├── class-autoloader.php
│   └── template-tags.php
└── traits/                   # Reusable traits
    └── trait-singleton.php
```

## Key Features

### 1. Modular Architecture
- **Features**: Each feature is a separate class that can be easily enabled/disabled
- **Singleton Pattern**: All classes use the Singleton trait for consistent instantiation
- **Autoloader**: Automatic class loading based on namespace conventions

### 2. Dynamic Menu System
- **MenuManager**: Handles all admin menu creation dynamically
- **Configurable**: Easy to add/remove menu items via configuration arrays
- **Consistent**: All admin pages follow the same pattern

### 3. Feature Management
Features can be easily enabled/disabled by commenting out lines in `inc/classes/class-get-sheilded-theme.php`:

```php
protected function __construct() {
    // Load class instances
    Frontend_Scripts::get_instance();
    Gutenberg_Blocks::get_instance();
    Templates::get_instance();
    Settings::get_instance();
    Languages::get_instance();
    Color_Palette::get_instance();
    Layout_Settings::get_instance();
    Block_Registry::get_instance();
    Menu_Manager::get_instance();

    $this->setup_hooks();
}

// To disable a feature, simply comment it out:
// Languages::get_instance();
// Color_Palette::get_instance();
```

### 4. Namespace Convention
- **Root**: `GetsheildedTheme\Inc\`
- **Classes**: `GetsheildedTheme\Inc\Classes\` (all classes including features)
- **Admin**: `GetsheildedTheme\Inc\Admin\`
- **Helpers**: `GetsheildedTheme\Inc\Helpers\`
- **Traits**: `GetsheildedTheme\Inc\Traits\`

### 5. File Naming Convention
- **Classes**: `class-{name}.php`
- **Traits**: `trait-{name}.php`
- **Helpers**: `class-{name}.php` or `{name}.php`

## Usage Examples

### Adding a New Feature
1. Create a new class in `inc/features/class-{feature-name}.php`
2. Use the Singleton trait
3. Add it to the features array in `class-theme.php`
4. Implement the `init()` method if needed

### Adding a New Admin Menu
1. Use the MenuManager to add menu items dynamically
2. Create corresponding page render methods
3. All menus automatically redirect to the settings page

### Adding Template Tags
1. Add functions to `inc/helpers/template-tags.php`
2. Use the `GetsheildedTheme\Inc\Helpers` namespace
3. Functions are automatically available in templates

## Benefits

1. **Maintainable**: Clear separation of concerns
2. **Scalable**: Easy to add new features
3. **Flexible**: Features can be enabled/disabled as needed
4. **Consistent**: Follows established patterns
5. **Organized**: Logical file structure
6. **Reusable**: Singleton pattern and traits promote code reuse

## Theme Initialization

The theme follows this initialization flow:

1. `functions.php` loads the autoloader and main theme class
2. `Get_Sheilded_Theme` class initializes the core `Theme` class
3. `Theme` class loads all active features
4. Each feature initializes its own functionality
5. MenuManager creates the admin interface

This structure makes the theme highly modular and easy to maintain while following WordPress best practices.
