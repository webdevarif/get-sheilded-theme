#!/bin/bash

echo "🚀 Setting up Get Shielded Theme..."

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js first."
    exit 1
fi

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ npm is not installed. Please install npm first."
    exit 1
fi

echo "📦 Installing dependencies..."

# Clear cache first
npm cache clean --force

# Try with legacy peer deps first
npm install --legacy-peer-deps

if [ $? -ne 0 ]; then
    echo "⚠️  Installation with legacy peer deps failed. Trying simplified approach..."
    
    # Try with the simplified package.json
    if [ -f "package-simple.json" ]; then
        echo "📦 Using simplified package.json..."
        cp package.json package-backup.json
        cp package-simple.json package.json
        npm install --legacy-peer-deps
        
        if [ $? -ne 0 ]; then
            echo "⚠️  Simplified package failed. Trying minimal approach..."
            
            # Try with minimal package.json
            if [ -f "package-minimal.json" ]; then
                cp package-minimal.json package.json
                npm install --legacy-peer-deps
                
                if [ $? -ne 0 ]; then
                    echo "❌ All installation attempts failed. Restoring original package.json..."
                    cp package-backup.json package.json
                    rm package-backup.json
                    exit 1
                fi
                
                echo "✅ Installation successful with minimal dependencies"
                echo "⚠️  Note: Some WordPress packages were excluded. Install them manually if needed."
            else
                echo "❌ Installation failed. Restoring original package.json..."
                cp package-backup.json package.json
                rm package-backup.json
                exit 1
            fi
        else
            echo "✅ Installation successful with simplified dependencies"
        fi
        
        rm package-backup.json
    else
        echo "❌ Installation failed"
        exit 1
    fi
fi

echo "🎨 Setting up ShadCN UI..."

# Install ShadCN CLI globally if not present
if ! command -v shadcn-ui &> /dev/null; then
    echo "📦 Installing ShadCN UI CLI..."
    npm install -g shadcn-ui@latest
fi

echo "🎯 Initializing ShadCN UI..."
npx shadcn-ui@latest init --yes --defaults

echo "📋 Adding essential ShadCN components..."
npx shadcn-ui@latest add button card input label tabs toast --yes

echo "🏗️  Building assets..."
npm run build

echo "✅ Setup complete!"
echo ""
echo "📖 Next steps:"
echo "1. Activate the theme in WordPress Admin"
echo "2. Run 'npm run dev' for development"
echo "3. Add more ShadCN components with 'npm run ui:add <component>'"
echo ""
echo "📚 Documentation:"
echo "- README.md - Full documentation"
echo "- setup-shadcn.md - ShadCN UI guide"
