# Get Shielded Theme

A modern WordPress theme built with Object-Oriented Programming, React admin interface, and Gutenberg blocks using ShadCN UI components.

## Features

- ✅ **Object-Oriented Architecture**: Clean, modular PHP code structure
- ✅ **React Admin Interface**: Modern admin panel built with Vite and React
- ✅ **Gutenberg Blocks**: Custom blocks with ShadCN UI styling
- ✅ **Separate Build Systems**: Webpack for frontend/Gutenberg, Vite for admin
- ✅ **ShadCN UI Components**: Latest CLI-based component system
- ✅ **GSAP Animations**: Professional animations with GSAP and ScrollTrigger
- ✅ **Tailwind CSS in SCSS**: Modern CSS with Tailwind integration
- ✅ **TypeScript Support**: Full TypeScript integration
- ✅ **Modern Development Workflow**: ESLint, Prettier, and automated builds

## Directory Structure

```
get-shielded-theme/
├── includes/                 # PHP Classes (OOP)
│   ├── Core/                # Core theme functionality
│   ├── Frontend/            # Frontend-specific classes
│   ├── Admin/               # Admin-specific classes
│   └── Blocks/              # Gutenberg blocks registry
├── src/
│   ├── admin/               # React admin interface (Vite)
│   ├── frontend/            # Frontend assets (Webpack)
│   ├── gutenberg/           # Gutenberg blocks (Webpack)
│   ├── components/ui/       # ShadCN UI components
│   ├── lib/                 # Utility functions
│   └── styles/              # Global styles
├── dist/                    # Compiled assets
├── webpack.*.js             # Webpack configurations
├── vite.admin.config.js     # Vite configuration for admin
└── package.json             # Dependencies and build scripts
```

## Installation

### Option 1: Automated Setup (Recommended)

1. **Clone or download** this theme to your WordPress themes directory:
   ```bash
   cd /path/to/wordpress/wp-content/themes/
   git clone <repository-url> get-shielded-theme
   cd get-shielded-theme
   ```

2. **Run the setup script**:
   ```bash
   # On Linux/Mac
   chmod +x setup.sh
   ./setup.sh

   # On Windows
   setup.bat
   ```

3. **Activate the theme** in WordPress Admin → Appearance → Themes

### Option 2: Manual Setup

1. **Clone the repository**:
   ```bash
   cd /path/to/wordpress/wp-content/themes/
   git clone <repository-url> get-shielded-theme
   cd get-shielded-theme
   ```

2. **Install dependencies** (if you encounter issues, use the alternative package.json):
   ```bash
   # Try this first
   npm install --legacy-peer-deps

   # If that fails, use simplified dependencies
   cp package-simple.json package.json
   npm install --legacy-peer-deps
   ```

3. **Initialize ShadCN UI**:
   ```bash
   npx shadcn-ui@latest init
   ```
   Choose: Default style, Slate base color, Yes to CSS variables

4. **Add essential ShadCN components**:
   ```bash
   npx shadcn-ui@latest add button card input label tabs toast
   ```

5. **Build assets**:
   ```bash
   # Development build with watch mode
   npm run dev

   # Production build
   npm run build
   ```

6. **Activate the theme** in WordPress Admin → Appearance → Themes

### Troubleshooting Installation

If you encounter dependency conflicts:

1. **Clear npm cache**:
   ```bash
   npm cache clean --force
   ```

2. **Delete node_modules and try again**:
   ```bash
   rm -rf node_modules package-lock.json
   npm install --legacy-peer-deps
   ```

3. **Use the simplified package.json**:
   ```bash
   cp package-simple.json package.json
   npm install --legacy-peer-deps
   ```

## Development

### Available Scripts

```bash
# Start development with watch mode for all builds
npm run dev

# Individual development builds
npm run dev:frontend    # Frontend assets (Webpack)
npm run dev:admin      # Admin React app (Vite)  
npm run dev:gutenberg  # Gutenberg blocks (Webpack)

# Production builds
npm run build          # Build all assets for production
npm run build:frontend # Build frontend only
npm run build:admin    # Build admin only
npm run build:gutenberg # Build Gutenberg only

# Code quality
npm run lint          # Run ESLint
npm run lint:fix      # Fix ESLint issues automatically
npm run type-check    # TypeScript type checking
```

### Build Systems

1. **Frontend** (Webpack):
   - Entry: `src/frontend/js/main.js`, `src/frontend/js/components.js`
   - Output: `dist/frontend/`
   - Features: Sass, PostCSS, Babel

2. **Admin** (Vite):
   - Entry: `src/admin/main.tsx`
   - Output: `dist/admin/`
   - Features: React, TypeScript, Tailwind CSS

3. **Gutenberg** (Webpack):
   - Entry: `src/gutenberg/index.js`
   - Output: `dist/gutenberg/`
   - Features: WordPress blocks, Sass, TypeScript

## Theme Architecture

### PHP Classes (OOP)

- **Theme.php**: Main theme class, handles WordPress hooks
- **Frontend/Scripts.php**: Frontend asset management
- **Admin/Scripts.php**: Admin interface and menu management
- **Blocks/BlockRegistry.php**: Gutenberg blocks registration

### React Admin Interface

The admin interface is built with React and Vite, providing a modern development experience:

- TypeScript support
- ShadCN UI components
- Hot module replacement
- Optimized builds

### Gutenberg Blocks

Custom blocks included:

1. **Hero Section**: Full-width hero with background image and overlay
2. **Feature Card**: Card component with icon, title, and description
3. **Testimonial**: Quote block with author attribution
4. **Call to Action**: Centered CTA section
5. **Pricing Table**: Responsive pricing grid

All blocks use ShadCN UI styling and are fully responsive.

## Customization

### Adding New Blocks

1. Create a new directory in `src/gutenberg/blocks/your-block/`
2. Add the block files:
   - `index.js` - Block registration and edit/save functions
   - `style.scss` - Frontend styles
   - `editor.scss` - Editor-only styles
3. Import in `src/gutenberg/index.js`
4. Add to the blocks array in `includes/Blocks/BlockRegistry.php`

### Modifying Admin Interface

The admin interface is in `src/admin/`. Key files:
- `main.tsx` - Entry point
- `App.tsx` - Main application component
- `components/ui/` - ShadCN UI components

### Frontend Customization

Frontend assets are in `src/frontend/`:
- `js/` - JavaScript files with GSAP integration
- `scss/` - Sass stylesheets with Tailwind CSS
- `scss/components/` - Component-specific styles

### GSAP Animations

The theme uses GSAP for professional animations:

**Available Animation Classes:**
- `.gsap-fade-in-up` - Fade in with upward motion
- `.gsap-scroll-trigger` - Trigger animations on scroll
- `.gsap-hover-lift` - Lift effect on hover
- `.gsap-hover-scale` - Scale effect on hover
- `.gsap-stagger-container` with `.gsap-stagger-item` - Staggered animations

**Custom Animations:**
```javascript
import { GSAPAnimations } from '@/lib/gsap-animations';

// Fade in animation
GSAPAnimations.fadeInUp('.my-element');

// Hero entrance
GSAPAnimations.heroEntrance();

// Stagger multiple elements
GSAPAnimations.staggerIn(['.item1', '.item2', '.item3']);
```

## ShadCN UI Integration

This theme includes a curated set of ShadCN UI components:

- Button
- Card
- Input
- Select
- Dialog
- Toast
- And many more...

Components are located in `src/components/ui/` and can be used in both Gutenberg blocks and the admin interface.

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11+ (with polyfills)
- Mobile browsers

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests and linting
5. Submit a pull request

## License

GPL v2 or later

## Support

For support and documentation, please visit [your-website.com](https://your-website.com).
