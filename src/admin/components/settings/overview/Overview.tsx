import React from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ThemeSettings } from '../../../services/api';

interface OverviewProps {
  settings: ThemeSettings;
}

export const Overview: React.FC<OverviewProps> = ({ settings }) => {
  return (
    <div className="space-y-6">
      <Card>
        <CardHeader>
          <CardTitle>Theme Overview</CardTitle>
          <CardDescription>
            Get sheilded Theme - A modern, professional WordPress theme with React-powered admin panel
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="space-y-2">
              <h4 className="text-sm font-medium">Theme Information</h4>
              <div className="space-y-1 text-sm text-muted-foreground">
                <p><strong>Version:</strong> 1.0.0</p>
                <p><strong>Author:</strong> Web Developer Arif</p>
                <p><strong>License:</strong> GPL v2 or later</p>
                <p><strong>WordPress:</strong> 6.0+</p>
                <p><strong>PHP:</strong> 7.4+</p>
              </div>
            </div>
            
            <div className="space-y-2">
              <h4 className="text-sm font-medium">Features</h4>
              <div className="flex flex-wrap gap-1">
                <Badge variant="secondary">OOP Architecture</Badge>
                <Badge variant="secondary">React Admin</Badge>
                <Badge variant="secondary">Gutenberg Blocks</Badge>
                <Badge variant="secondary">ShadCN UI</Badge>
                <Badge variant="secondary">Tailwind CSS</Badge>
                <Badge variant="secondary">Custom Templates</Badge>
                <Badge variant="secondary">GSAP Animations</Badge>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Current Settings Summary</CardTitle>
          <CardDescription>
            Overview of your current theme configuration
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="space-y-2">
              <h4 className="text-sm font-medium">Typography</h4>
              <div className="text-sm text-muted-foreground">
                <p><strong>Body Font:</strong> {settings.typography.bodyFontFamily}</p>
                <p><strong>Heading Font:</strong> {settings.typography.headingFontFamily}</p>
                <p><strong>Body Size:</strong> {settings.typography.bodySize}</p>
              </div>
            </div>
            
            <div className="space-y-2">
              <h4 className="text-sm font-medium">Colors</h4>
              <div className="flex flex-wrap gap-1">
                <div className="flex items-center gap-1">
                  <div 
                    className="w-3 h-3 rounded border" 
                    style={{ backgroundColor: settings.colors.primary }}
                  ></div>
                  <span className="text-xs">Primary</span>
                </div>
                <div className="flex items-center gap-1">
                  <div 
                    className="w-3 h-3 rounded border" 
                    style={{ backgroundColor: settings.colors.secondary }}
                  ></div>
                  <span className="text-xs">Secondary</span>
                </div>
                <div className="flex items-center gap-1">
                  <div 
                    className="w-3 h-3 rounded border" 
                    style={{ backgroundColor: settings.colors.accent }}
                  ></div>
                  <span className="text-xs">Accent</span>
                </div>
              </div>
            </div>
            
            <div className="space-y-2">
              <h4 className="text-sm font-medium">Layout</h4>
              <div className="text-sm text-muted-foreground">
                <p><strong>Container:</strong> {settings.layout.containerMaxWidth}</p>
                <p><strong>Border Radius:</strong> {settings.layout.borderRadius}</p>
                <p><strong>Spacing:</strong> {settings.layout.spacing}</p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Quick Actions</CardTitle>
          <CardDescription>
            Common tasks and shortcuts
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="flex flex-wrap gap-2">
            <Button variant="outline" size="sm">
              View Documentation
            </Button>
            <Button variant="outline" size="sm">
              Check for Updates
            </Button>
            <Button variant="outline" size="sm">
              Export Settings
            </Button>
            <Button variant="outline" size="sm">
              Import Settings
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};
