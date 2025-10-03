const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
    ...defaultConfig,
    entry: {
        // Admin React components
        'admin/template-settings': './src/admin/js/template-settings.jsx',
        
        // Gutenberg blocks
        'gutenberg/blocks': './src/gutenberg/index.js',
        
        // Frontend
        'frontend/main': './src/frontend/js/main.js',
    },
    externals: {
        // WordPress provides React globally
        react: 'React',
        'react-dom': 'ReactDOM',
        // Bundle react-select with our components
    },
};
