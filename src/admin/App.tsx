import React from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Settings, Blocks, Shield } from 'lucide-react';

const App: React.FC = () => {
  return (
    <div className="min-h-screen bg-gray-50 p-6">
      <div className="max-w-7xl mx-auto">
        <div className="mb-8">
          <div className="flex items-center space-x-3 mb-4">
            <Shield className="h-8 w-8 text-blue-600" />
            <h1 className="text-3xl font-bold text-gray-900">Get Shielded Theme - Live! 🚀</h1>
          </div>
          <p className="text-gray-600">
            Modern WordPress theme administration with React, GSAP, and ShadCN UI
          </p>
        </div>

        <Tabs defaultValue="overview" className="w-full">
          <TabsList className="grid w-full grid-cols-4">
            <TabsTrigger value="overview">Overview</TabsTrigger>
            <TabsTrigger value="settings">Settings</TabsTrigger>
            <TabsTrigger value="blocks">Blocks</TabsTrigger>
            <TabsTrigger value="help">Help</TabsTrigger>
          </TabsList>

          <TabsContent value="overview" className="mt-6">
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
                  <Button className="w-full">
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
                  <Button className="w-full" variant="outline">
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

          <TabsContent value="settings" className="mt-6">
            <Card>
              <CardHeader>
                <CardTitle>Theme Settings</CardTitle>
                <CardDescription>
                  Configure your theme preferences and options
                </CardDescription>
              </CardHeader>
              <CardContent>
                <p className="text-gray-600">Settings panel coming soon...</p>
              </CardContent>
            </Card>
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
            <Card>
              <CardHeader>
                <CardTitle>Help & Documentation</CardTitle>
                <CardDescription>
                  Resources and guides for using the theme
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div>
                    <h4 className="font-medium mb-2">Quick Start</h4>
                    <p className="text-sm text-gray-600 mb-2">1. Run <code>npm run dev</code> for development</p>
                    <p className="text-sm text-gray-600 mb-2">2. Add ShadCN components with <code>npx shadcn@latest add [component]</code></p>
                    <p className="text-sm text-gray-600">3. Use GSAP animation classes for smooth effects</p>
                  </div>
                  
                  <div>
                    <h4 className="font-medium mb-2">Animation Classes</h4>
                    <ul className="text-sm text-gray-600 space-y-1">
                      <li>• <code>.gsap-fade-in-up</code> - Fade in with upward motion</li>
                      <li>• <code>.gsap-scroll-trigger</code> - Animate on scroll</li>
                      <li>• <code>.gsap-hover-lift</code> - Lift effect on hover</li>
                    </ul>
                  </div>
                </div>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  );
};

export default App;
