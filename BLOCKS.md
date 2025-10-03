# Get Shielded Theme - Gutenberg Blocks

## 🛡️ Custom Block Category: "Get Shielded"

All custom blocks are organized under the **"Get Shielded"** category with a shield icon for easy identification.

## 📦 Available Blocks

### 1. Header 1 (`get-shielded/header-1`)
- **Description**: A responsive header with desktop/mobile logo options and smooth animations
- **Icon**: Admin Site
- **Supports**: Wide, Full alignment
- **Features**:
  - Desktop/Mobile logo support
  - Logo text toggle
  - Navigation items
  - CTA button
  - Language switcher
  - Mobile hamburger menu
  - Custom colors

### 2. Hero Section (`get-shielded/hero-section`)
- **Description**: A modern hero section with customizable content
- **Icon**: Cover Image
- **Supports**: Wide, Full alignment
- **Features**:
  - Background image support
  - Overlay color customization
  - Minimum height control
  - InnerBlocks for flexible content
  - Template with heading, paragraph, and button

### 3. Feature Card (`get-shielded/feature-card`)
- **Description**: A modern feature card with icon, title, and description
- **Icon**: ID Alt
- **Supports**: No alignment (fixed width)
- **Features**:
  - Icon selection (Shield, Star, Heart, Zap, Award, Check Circle)
  - Card styles (Default, Outlined, Filled)
  - Rich text editing
  - Custom styling options

### 4. Testimonial (`get-shielded/testimonial`)
- **Description**: A testimonial block with quote, author, and company
- **Icon**: Format Quote
- **Features**:
  - Quote text
  - Author name
  - Company name
  - Rich text editing

### 5. Call to Action (`get-shielded/call-to-action`)
- **Description**: A call-to-action section with customizable content
- **Icon**: Megaphone
- **Supports**: Wide, Full alignment
- **Features**:
  - InnerBlocks for flexible content
  - Template with heading, paragraph, and button

### 6. Pricing Table (`get-shielded/pricing-table`)
- **Description**: A pricing table with multiple columns
- **Icon**: Money Alt
- **Supports**: Wide, Full alignment
- **Features**:
  - InnerBlocks for flexible content
  - Template with multiple pricing columns

## 🎨 Block Category Features

- **Custom Category**: All blocks are grouped under "Get Shielded"
- **Shield Icon**: Easy visual identification
- **Consistent Naming**: All blocks use `get-shielded/` namespace
- **WordPress Integration**: Properly integrated with WordPress block editor
- **Responsive Design**: All blocks support responsive layouts
- **Custom Styling**: Each block has its own SCSS files for styling

## 🚀 Usage

1. **Add Block**: Click the "+" button in the block editor
2. **Find Category**: Look for "Get Shielded" category
3. **Select Block**: Choose the block you need
4. **Customize**: Use the block settings panel to customize
5. **Preview**: See your changes in real-time

## 🛠️ Development

### Adding New Blocks

1. Create block folder in `src/gutenberg/blocks/`
2. Use `get-shielded/block-name` namespace
3. Set category to `get-shielded`
4. Import in `src/gutenberg/index.js`
5. Build with `npm run build`

### Block Structure

```
src/gutenberg/blocks/block-name/
├── index.js          # Block registration and editor
├── style.scss        # Frontend styles
└── editor.scss       # Editor styles
```

## 📝 Notes

- All blocks use the `get-shielded-theme` text domain
- Blocks are built with WordPress scripts for optimal performance
- Each block has proper accessibility features
- Responsive design is built-in
- Custom styling options are available for each block

---

**Built with ❤️ for Get Shielded Theme**
