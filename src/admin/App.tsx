import React, { useEffect, useState } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Settings, Blocks, Shield, Home } from 'lucide-react';
import { Toaster } from 'sonner';
import { SWRProvider } from '@/components/providers/SWRProvider';
import SettingsPage from './Settings';

const App: React.FC = () => {
  const [currentTab, setCurrentTab] = useState('welcome');

  // Get tab from URL parameter
  useEffect(() => {
    console.log('App component mounted');
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab') || 'welcome';
    console.log('Current tab from URL:', tab);
    setCurrentTab(tab);
  }, []);

  // Update URL when tab changes
  const handleTabChange = (value: string) => {
    setCurrentTab(value);
    const url = new URL(window.location.href);
    url.searchParams.set('tab', value);
    window.history.replaceState({}, '', url.toString());
  };

  console.log('App component rendering, currentTab:', currentTab);

  return (
    <SWRProvider>
      <div className="min-h-screen bg-gray-50 p-6">
        <div className="max-w-7xl mx-auto">
          <div className="mb-8">
            <div className="flex items-center space-x-3 mb-4">
              <Shield className="h-8 w-8 text-blue-600" />
              <h1 className="text-3xl font-bold text-gray-900">Get sheilded Theme - Live! 🚀</h1>
            </div>
            <p className="text-gray-600">
              Modern WordPress theme administration with React, GSAP, and ShadCN UI
            </p>
          </div>

        <Tabs value={currentTab} onValueChange={handleTabChange} className="w-full">
          <TabsList className="grid w-full grid-cols-4">
            <TabsTrigger value="welcome">Welcome</TabsTrigger>
            <TabsTrigger value="theme">Theme</TabsTrigger>
            <TabsTrigger value="blocks">Blocks</TabsTrigger>
            <TabsTrigger value="help">Help</TabsTrigger>
          </TabsList>

          <TabsContent value="welcome" className="mt-6">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center space-x-2">
                    <Settings className="h-5 w-5" />
                    <span>Theme Settings</span>
                  </CardTitle>
                  <CardDescription>
                    Configure your theme options and customizations
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <Button 
                    className="w-full"
                    onClick={() => handleTabChange('theme')}
                  >
                    Open Settings
                  </Button>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center space-x-2">
                    <Blocks className="h-5 w-5" />
                    <span>Block Manager</span>
                  </CardTitle>
                  <CardDescription>
                    Manage your Gutenberg blocks and components
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <Button 
                    className="w-full" 
                    variant="outline"
                    onClick={() => handleTabChange('blocks')}
                  >
                    Manage Blocks
                  </Button>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle>Quick Stats</CardTitle>
                  <CardDescription>
                    Overview of your theme usage
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-2">
                    <div className="flex justify-between">
                      <span className="text-sm text-gray-600">Active Blocks:</span>
                      <span className="font-medium">5</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-gray-600">Theme Version:</span>
                      <span className="font-medium">1.0.0</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-gray-600">GSAP Animations:</span>
                      <span className="font-medium text-green-600">Active</span>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>

            <div className="mt-8">
              <Card>
                <CardHeader>
                  <CardTitle>Theme Information</CardTitle>
                  <CardDescription>
                    Current theme configuration and status
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <h4 className="font-medium mb-2">Features</h4>
                      <ul className="text-sm text-gray-600 space-y-1">
                        <li>✓ Object-Oriented Architecture</li>
                        <li>✓ React Admin Interface</li>
                        <li>✓ Gutenberg Blocks with ShadCN UI</li>
                        <li>✓ GSAP Animations</li>
                        <li>✓ Tailwind CSS in SCSS</li>
                        <li>✓ TypeScript Support</li>
                      </ul>
                    </div>
                    <div>
                      <h4 className="font-medium mb-2">Build System</h4>
                      <ul className="text-sm text-gray-600 space-y-1">
                        <li>• Frontend: Webpack + GSAP + Sass</li>
                        <li>• Admin: Vite + React + ShadCN</li>
                        <li>• Gutenberg: Webpack + TypeScript</li>
                        <li>• Styling: Tailwind CSS + ShadCN UI</li>
                      </ul>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          <TabsContent value="theme" className="mt-6">
            <SettingsPage />
          </TabsContent>

          <TabsContent value="blocks" className="mt-6">
            <Card>
              <CardHeader>
                <CardTitle>Gutenberg Blocks</CardTitle>
                <CardDescription>
                  Manage your custom Gutenberg blocks
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {['Hero Section', 'Feature Card', 'Testimonial', 'Call to Action', 'Pricing Table'].map((block) => (
                    <div key={block} className="flex items-center justify-between p-3 border rounded">
                      <span className="font-medium">{block}</span>
                      <span className="text-sm text-green-600">Active</span>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="help" className="mt-6">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card>
                <CardHeader>
                  <CardTitle>Quick Start Guide</CardTitle>
                  <CardDescription>
                    Get up and running with the theme quickly
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    <div>
                      <h4 className="font-medium mb-2">Development Setup</h4>
                      <ul className="text-sm text-gray-600 space-y-1">
                        <li>1. Run <code className="bg-gray-100 px-1 rounded">npm run dev</code> for development</li>
                        <li>2. Run <code className="bg-gray-100 px-1 rounded">npm run build</code> for production</li>
                        <li>3. Run <code className="bg-gray-100 px-1 rounded">npm run build:admin</code> for admin assets</li>
                      </ul>
                    </div>
                    
                    <div>
                      <h4 className="font-medium mb-2">Adding Components</h4>
                      <ul className="text-sm text-gray-600 space-y-1">
                        <li>• <code className="bg-gray-100 px-1 rounded">npx shadcn@latest add [component]</code></li>
                        <li>• Use ShadCN UI for admin interface</li>
                        <li>• Use Tailwind CSS for frontend styling</li>
                      </ul>
                    </div>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle>Animation Classes</CardTitle>
                  <CardDescription>
                    GSAP animation classes for smooth effects
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-2">
                    <div className="flex justify-between">
                      <code className="bg-gray-100 px-2 py-1 rounded text-xs">.gsap-fade-in-up</code>
                      <span className="text-xs text-gray-600">Fade in with upward motion</span>
                    </div>
                    <div className="flex justify-between">
                      <code className="bg-gray-100 px-2 py-1 rounded text-xs">.gsap-scroll-trigger</code>
                      <span className="text-xs text-gray-600">Animate on scroll</span>
                    </div>
                    <div className="flex justify-between">
                      <code className="bg-gray-100 px-2 py-1 rounded text-xs">.gsap-hover-lift</code>
                      <span className="text-xs text-gray-600">Lift effect on hover</span>
                    </div>
                    <div className="flex justify-between">
                      <code className="bg-gray-100 px-2 py-1 rounded text-xs">.gsap-scale-in</code>
                      <span className="text-xs text-gray-600">Scale in animation</span>
                    </div>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle>Theme Structure</CardTitle>
                  <CardDescription>
                    Understanding the theme architecture
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-2 text-sm">
                    <div className="flex justify-between">
                      <span className="font-medium">Frontend:</span>
                      <span className="text-gray-600">Webpack + GSAP + Sass</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="font-medium">Admin:</span>
                      <span className="text-gray-600">Vite + React + ShadCN</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="font-medium">Gutenberg:</span>
                      <span className="text-gray-600">Webpack + TypeScript</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="font-medium">Styling:</span>
                      <span className="text-gray-600">Tailwind CSS + ShadCN UI</span>
                    </div>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle>Support & Resources</CardTitle>
                  <CardDescription>
                    Get help and find resources
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-3">
                    <div>
                      <h4 className="font-medium mb-1">Documentation</h4>
                      <p className="text-sm text-gray-600">Check the theme documentation for detailed guides</p>
                    </div>
                    <div>
                      <h4 className="font-medium mb-1">GitHub Repository</h4>
                      <p className="text-sm text-gray-600">View source code and submit issues</p>
                    </div>
                    <div>
                      <h4 className="font-medium mb-1">Community</h4>
                      <p className="text-sm text-gray-600">Join our community for support and updates</p>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>
        </Tabs>
        </div>
        <Toaster />
      </div>
    </SWRProvider>
  );
};

export default App;
