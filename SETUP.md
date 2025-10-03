# Get Sheilded Theme - Streamlined Setup

## 🚀 Quick Start

### 1. Installation
```bash
# Clone or download the theme
cd wp-content/themes/
git clone <repository-url> get-sheilded-theme
cd get-sheilded-theme
```

### 2. Setup
```bash
# Run the cleanup script (one-time only)
php cleanup.php

# Install dependencies and build
npm run setup
```

### 3. Activate
- Go to WordPress Admin → Appearance → Themes
- Activate "Get Sheilded Theme"

## 📁 New Streamlined Structure

```
get-sheilded-theme/
├── inc/
│   ├── core/                    # Core theme functionality
│   │   └── class-theme-core.php
│   ├── features/                # Modular features
│   │   ├── class-templates.php
│   │   ├── class-admin.php
│   │   └── class-blocks.php
│   ├── helpers/                 # Helper functions
│   └── traits/                  # Reusable traits
├── src/
│   ├── components/              # Unified components
│   ├── admin/js/               # React admin components
│   ├── frontend/js/            # Frontend JavaScript
│   └── gutenberg/              # Gutenberg blocks
├── dist/                       # Built assets
├── webpack.unified.js          # Single build configuration
└── package.json               # Simplified dependencies
```

## 🎯 Key Features

### ✅ Modular Architecture
- **Core System**: Centralized theme management
- **Feature System**: Easy to enable/disable features
- **Component System**: Reusable React components

### ✅ Unified Build System
- **Single Webpack Config**: One config for everything
- **Automatic Asset Management**: WordPress handles dependencies
- **Development Mode**: Hot reload for all components

### ✅ Reusable Components
- **CustomSelect**: WordPress-styled select components
- **CustomInput**: Consistent input fields
- **FormField**: Standardized form layouts

## 🛠️ Development

### Development Mode
```bash
npm run dev
```
- Watches all files for changes
- Hot reloads in browser
- Development optimizations

### Production Build
```bash
npm run build
```
- Optimized production assets
- Minified code
- Tree shaking

### Clean Build
```bash
npm run clean
npm run build
```

## 🎨 Customization

### Adding New Features
1. Create new class in `inc/features/class-your-feature.php`
2. Use Singleton trait
3. Register in `Theme_Core::load_core_features()`

### Adding New Components
1. Create component in `src/components/`
2. Export from `src/components/index.js`
3. Import anywhere: `import { YourComponent } from '@components'`

### Customizing Styles
- **Admin**: Modify `src/admin/js/components/CustomSelect.jsx`
- **Frontend**: Edit `src/frontend/scss/main.scss`
- **Blocks**: Update `src/gutenberg/styles/`

## 📦 Available Commands

```bash
npm run dev          # Development mode with watch
npm run build        # Production build
npm run clean        # Clean dist folder
npm run setup        # Install + build
```

## 🔧 Configuration

### Theme Features
Enable/disable features in `inc/core/class-theme-core.php`:

```php
private function load_core_features() {
    $this->register_feature('templates', Templates::class);
    $this->register_feature('admin', Admin::class);
    $this->register_feature('blocks', Blocks::class);
    // Add your features here
}
```

### Build Configuration
Modify `webpack.unified.js` for:
- Entry points
- Output paths
- Loaders
- Plugins

## 🎉 Benefits

- ✅ **Simplified**: Single build system
- ✅ **Modular**: Easy to add/remove features
- ✅ **Reusable**: Components work everywhere
- ✅ **Maintainable**: Clean, organized code
- ✅ **Fast**: Optimized builds and loading
- ✅ **Modern**: React + WordPress best practices

## 🆘 Troubleshooting

### Build Issues
```bash
# Clean and rebuild
npm run clean
npm run build
```

### Component Issues
- Check imports in `src/components/index.js`
- Verify webpack aliases in `webpack.unified.js`

### Feature Issues
- Ensure feature class uses Singleton trait
- Check registration in `Theme_Core`

## 📚 Next Steps

1. **Customize Components**: Modify colors and styles
2. **Add Features**: Create new feature classes
3. **Extend Admin**: Add more React components
4. **Create Blocks**: Build custom Gutenberg blocks

---

**🎯 This streamlined theme is now easy to setup, maintain, and extend!**
