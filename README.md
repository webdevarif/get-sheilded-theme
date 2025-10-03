# Get Sheilded Theme - Clean & Reusable

A modern WordPress theme built with **@wordpress/scripts** for optimal performance and maintainability.

## 🚀 Quick Start

```bash
# Install dependencies
npm install

# Build for production
npm run build

# Development with watch mode
npm run dev

# Clean build files
npm run clean
```

## 📁 Project Structure

```
src/
├── components/           # Reusable React components
│   ├── Select.jsx       # Custom react-select wrapper
│   ├── Input.jsx        # Custom input component
│   ├── FormField.jsx    # Form field wrapper
│   └── index.js         # Component exports
├── admin/
│   └── js/
│       └── template-settings.jsx  # Template settings React component
├── gutenberg/
│   └── index.js         # Gutenberg blocks entry point
└── frontend/
    └── js/
        └── main.js      # Frontend JavaScript entry point

build/                   # Generated files (WordPress scripts output)
├── admin/
├── gutenberg/
└── frontend/
```

## 🛠️ Build System

This theme uses **@wordpress/scripts** for:

- ✅ **Automatic dependency management** - WordPress handles React/ReactDOM
- ✅ **Asset optimization** - Minification, tree-shaking, code splitting
- ✅ **WordPress integration** - Proper asset.php files with dependencies
- ✅ **Development workflow** - Hot reloading and watch mode
- ✅ **Production builds** - Optimized bundles

## 🎯 Features

### Reusable Components
- **CustomSelect** - WordPress-styled react-select component
- **CustomInput** - Consistent input styling
- **FormField** - Label + help text wrapper

### Template Settings
- **React-powered** admin interface
- **Template Type** - Header/Footer selection
- **Display Options** - Entire site or specific pages
- **Page Selection** - Multi-select with search
- **Priority System** - Template hierarchy

### Gutenberg Blocks
- **Custom blocks** with modern React
- **Block editor** integration
- **Responsive design** support

## 🔧 Development

### Adding New Components

1. Create component in `src/components/`
2. Export from `src/components/index.js`
3. Import in your React files: `import { CustomSelect } from '../../components'`

### Adding New Admin Pages

1. Create React component in `src/admin/js/`
2. Add entry point to `webpack.config.js`
3. Enqueue in `functions.php` using WordPress scripts asset files

### Adding New Blocks

1. Create block in `src/gutenberg/blocks/`
2. Register in `src/gutenberg/index.js`
3. Build with `npm run build`

## 📦 Dependencies

- **@wordpress/scripts** - Build system
- **@wordpress/components** - WordPress UI components
- **@wordpress/element** - React hooks and utilities
- **react-select** - Advanced select component
- **GSAP** - Animation library

## 🎨 Styling

- **Tailwind CSS** - Utility-first CSS framework
- **SCSS** - Enhanced CSS with variables and mixins
- **WordPress admin** - Consistent with WordPress design system

## 🚀 Production

```bash
npm run build
```

This generates optimized files in the `build/` directory:
- Minified JavaScript bundles
- Optimized CSS files
- Asset dependency files for WordPress

## 📝 Notes

- **WordPress scripts** automatically handles React externals
- **Asset files** (.asset.php) contain dependency information
- **Build folder** is used instead of `dist/` (WordPress scripts convention)
- **Components are reusable** across admin and frontend
- **Clean architecture** with separation of concerns

---

**Built with ❤️ using @wordpress/scripts**