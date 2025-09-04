import React from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';

export const Blocks: React.FC = () => {
  const blocks = [
    {
      name: 'Hero Section',
      description: 'Full-width hero with title, subtitle, and CTA button',
      category: 'Layout',
      status: 'Active',
    },
    {
      name: 'Feature Grid',
      description: 'Grid layout for showcasing features or services',
      category: 'Content',
      status: 'Active',
    },
    {
      name: 'Testimonial Carousel',
      description: 'Sliding carousel for customer testimonials',
      category: 'Content',
      status: 'Active',
    },
    {
      name: 'Pricing Table',
      description: 'Compare pricing plans with features',
      category: 'E-commerce',
      status: 'Active',
    },
    {
      name: 'Contact Form',
      description: 'Contact form with validation and email sending',
      category: 'Forms',
      status: 'Active',
    },
    {
      name: 'Team Members',
      description: 'Display team members with photos and bios',
      category: 'Content',
      status: 'Active',
    },
  ];

  const categories = ['All', 'Layout', 'Content', 'E-commerce', 'Forms'];

  return (
    <div className="space-y-6">
      <Card>
        <CardHeader>
          <CardTitle>Gutenberg Blocks</CardTitle>
          <CardDescription>
            Manage your custom Gutenberg blocks and their settings
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <div className="flex flex-wrap gap-2">
              {categories.map((category) => (
                <Button
                  key={category}
                  variant={category === 'All' ? 'default' : 'outline'}
                  size="sm"
                >
                  {category}
                </Button>
              ))}
            </div>

            <Separator />

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {blocks.map((block, index) => (
                <Card key={index} className="p-4">
                  <div className="space-y-3">
                    <div className="flex items-start justify-between">
                      <div>
                        <h4 className="font-medium">{block.name}</h4>
                        <p className="text-sm text-muted-foreground">{block.description}</p>
                      </div>
                      <Badge variant="secondary">{block.status}</Badge>
                    </div>
                    
                    <div className="flex items-center justify-between">
                      <Badge variant="outline">{block.category}</Badge>
                      <div className="flex gap-2">
                        <Button variant="outline" size="sm">
                          Edit
                        </Button>
                        <Button variant="outline" size="sm">
                          Settings
                        </Button>
                      </div>
                    </div>
                  </div>
                </Card>
              ))}
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Block Settings</CardTitle>
          <CardDescription>
            Global settings for all blocks
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="space-y-2">
              <h4 className="text-sm font-medium">Animation Settings</h4>
              <div className="space-y-2">
                <label className="flex items-center space-x-2">
                  <input type="checkbox" defaultChecked />
                  <span className="text-sm">Enable animations</span>
                </label>
                <label className="flex items-center space-x-2">
                  <input type="checkbox" defaultChecked />
                  <span className="text-sm">Reduce motion for accessibility</span>
                </label>
              </div>
            </div>

            <div className="space-y-2">
              <h4 className="text-sm font-medium">Performance</h4>
              <div className="space-y-2">
                <label className="flex items-center space-x-2">
                  <input type="checkbox" defaultChecked />
                  <span className="text-sm">Lazy load images</span>
                </label>
                <label className="flex items-center space-x-2">
                  <input type="checkbox" />
                  <span className="text-sm">Preload critical blocks</span>
                </label>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};
