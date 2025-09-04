# ShadCN UI Setup Guide

This guide explains how to set up ShadCN UI components using the latest CLI.

## Initial Setup

1. **Install dependencies:**
   ```bash
   npm install
   ```

2. **Initialize ShadCN UI (run this once):**
   ```bash
   npm run ui:init
   ```
   
   When prompted, choose:
   - Style: `Default`
   - Base color: `Slate`
   - Use CSS variables: `Yes`

## Adding Components

Use the following commands to add ShadCN UI components:

### Essential Components for Admin Interface

```bash
# Core components
npm run ui:add button
npm run ui:add card
npm run ui:add input
npm run ui:add label
npm run ui:add select
npm run ui:add textarea

# Layout components
npm run ui:add separator
npm run ui:add tabs
npm run ui:add accordion

# Feedback components
npm run ui:add toast
npm run ui:add alert
npm run ui:add progress

# Navigation components
npm run ui:add dropdown-menu
npm run ui:add popover
npm run ui:add tooltip

# Form components
npm run ui:add checkbox
npm run ui:add radio-group
npm run ui:add switch
npm run ui:add slider

# Data display
npm run ui:add table
npm run ui:add badge
npm run ui:add avatar

# Overlay components
npm run ui:add dialog
npm run ui:add sheet
npm run ui:add alert-dialog
```

### Components for Gutenberg Blocks

```bash
# Additional components for blocks
npm run ui:add calendar
npm run ui:add command
npm run ui:add context-menu
npm run ui:add hover-card
npm run ui:add menubar
npm run ui:add navigation-menu
npm run ui:add scroll-area
```

## Usage in Admin Interface

After adding components, update your admin interface:

```typescript
// src/admin/App.tsx
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

// Use the components as normal
<Card>
  <CardHeader>
    <CardTitle>Settings</CardTitle>
  </CardHeader>
  <CardContent>
    <Button>Save Changes</Button>
  </CardContent>
</Card>
```

## Usage in Gutenberg Blocks

For Gutenberg blocks, you can create wrapper components:

```javascript
// src/gutenberg/components/ShadcnButton.js
import { Button } from '@/components/ui/button';
import { createElement } from '@wordpress/element';

export const ShadcnButton = ({ children, ...props }) => {
  return createElement(Button, props, children);
};
```

## File Structure After Setup

After running the setup commands, your project will have:

```
src/
├── components/
│   └── ui/              # Auto-generated ShadCN components
│       ├── button.tsx
│       ├── card.tsx
│       ├── input.tsx
│       └── ...
├── lib/
│   └── utils.ts         # Utility functions (cn, etc.)
└── styles/
    └── globals.css      # Updated with component styles
```

## Configuration Files

The setup uses these configuration files:

- `components.json` - ShadCN UI configuration
- `tailwind.config.js` - Tailwind CSS configuration
- `tsconfig.json` - TypeScript paths for imports

## Important Notes

1. **Always use the CLI:** Use `npm run ui:add <component>` instead of manual installation
2. **Import paths:** Use `@/components/ui/...` for imports
3. **Customization:** Modify components in `src/components/ui/` as needed
4. **Version updates:** Components are auto-updated when you re-run the CLI

## Troubleshooting

If you encounter issues:

1. **Clear node_modules:** `rm -rf node_modules && npm install`
2. **Re-initialize:** Delete `components.json` and run `npm run ui:init` again
3. **Check paths:** Ensure `tsconfig.json` has correct path mappings

## Next Steps

1. Run `npm run ui:init` to initialize
2. Add components as needed with `npm run ui:add <component>`
3. Update your admin interface to use the new components
4. Test the components in both admin and Gutenberg contexts
