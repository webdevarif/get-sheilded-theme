@echo off
echo 🚀 Setting up Get Shielded Theme...

:: Check if Node.js is installed
node --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Node.js is not installed. Please install Node.js first.
    pause
    exit /b 1
)

:: Check if npm is installed
npm --version >nul 2>&1
if errorlevel 1 (
    echo ❌ npm is not installed. Please install npm first.
    pause
    exit /b 1
)

echo 📦 Installing dependencies...

:: Try with legacy peer deps first
npm install --legacy-peer-deps

if errorlevel 1 (
    echo ⚠️  Installation with legacy peer deps failed. Trying alternative approach...
    
    :: Clear cache and try again
    npm cache clean --force
    
    :: Try with the simplified package.json
    if exist "package-simple.json" (
        echo 📦 Using simplified package.json...
        copy package.json package-backup.json >nul
        copy package-simple.json package.json >nul
        npm install --legacy-peer-deps
        
        if errorlevel 1 (
            echo ❌ Installation failed. Restoring original package.json...
            copy package-backup.json package.json >nul
            del package-backup.json >nul
            pause
            exit /b 1
        )
        
        echo ✅ Installation successful with simplified dependencies
        del package-backup.json >nul
    ) else (
        echo ❌ Installation failed
        pause
        exit /b 1
    )
)

echo 🎨 Setting up ShadCN UI...

:: Install ShadCN CLI globally if not present
where shadcn-ui >nul 2>&1
if errorlevel 1 (
    echo 📦 Installing ShadCN UI CLI...
    npm install -g shadcn-ui@latest
)

echo 🎯 Initializing ShadCN UI...
npx shadcn-ui@latest init --yes --defaults

echo 📋 Adding essential ShadCN components...
npx shadcn-ui@latest add button card input label tabs toast --yes

echo 🏗️  Building assets...
npm run build

echo ✅ Setup complete!
echo.
echo 📖 Next steps:
echo 1. Activate the theme in WordPress Admin
echo 2. Run 'npm run dev' for development
echo 3. Add more ShadCN components with 'npm run ui:add ^<component^>'
echo.
echo 📚 Documentation:
echo - README.md - Full documentation
echo - setup-shadcn.md - ShadCN UI guide
pause
