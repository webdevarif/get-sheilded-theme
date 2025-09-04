import React from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';

export const Help: React.FC = () => {
  const faqs = [
    {
      question: "How do I customize the theme colors?",
      answer: "Go to Theme Settings > Colors tab and use the color pickers to customize your color palette. Changes are applied in real-time."
    },
    {
      question: "How do I add custom Gutenberg blocks?",
      answer: "Custom blocks are automatically available in the Gutenberg editor. Look for the 'Get sheilded' category in the block inserter."
    },
    {
      question: "How do I create custom header/footer templates?",
      answer: "Go to Templates in the admin menu, create a new template, select 'Header' or 'Footer' type, and design using Gutenberg blocks."
    },
    {
      question: "How do I update the theme?",
      answer: "The theme will show update notifications in your WordPress admin. Always backup your site before updating."
    },
    {
      question: "How do I get support?",
      answer: "Visit our support forum or contact us through the theme's official website for assistance."
    }
  ];

  const resources = [
    {
      title: "Documentation",
      description: "Complete theme documentation and guides",
      type: "Guide",
      url: "#"
    },
    {
      title: "Video Tutorials",
      description: "Step-by-step video tutorials",
      type: "Video",
      url: "#"
    },
    {
      title: "Support Forum",
      description: "Get help from the community",
      type: "Community",
      url: "#"
    },
    {
      title: "GitHub Repository",
      description: "View source code and contribute",
      type: "Code",
      url: "#"
    }
  ];

  return (
    <div className="space-y-6">
      <Card>
        <CardHeader>
          <CardTitle>Frequently Asked Questions</CardTitle>
          <CardDescription>
            Common questions and answers about the Get sheilded Theme
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          {faqs.map((faq, index) => (
            <div key={index} className="space-y-2">
              <h4 className="font-medium text-sm">{faq.question}</h4>
              <p className="text-sm text-muted-foreground">{faq.answer}</p>
              {index < faqs.length - 1 && <Separator className="mt-4" />}
            </div>
          ))}
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Resources & Support</CardTitle>
          <CardDescription>
            Helpful resources to get the most out of your theme
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {resources.map((resource, index) => (
              <Card key={index} className="p-4">
                <div className="space-y-2">
                  <div className="flex items-start justify-between">
                    <h4 className="font-medium">{resource.title}</h4>
                    <Badge variant="outline">{resource.type}</Badge>
                  </div>
                  <p className="text-sm text-muted-foreground">{resource.description}</p>
                  <Button variant="outline" size="sm" className="w-full">
                    Visit Resource
                  </Button>
                </div>
              </Card>
            ))}
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>System Information</CardTitle>
          <CardDescription>
            Technical details about your WordPress installation
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div className="space-y-2">
              <div className="flex justify-between">
                <span className="text-muted-foreground">WordPress Version:</span>
                <span>6.4</span>
              </div>
              <div className="flex justify-between">
                <span className="text-muted-foreground">PHP Version:</span>
                <span>8.1.0</span>
              </div>
              <div className="flex justify-between">
                <span className="text-muted-foreground">Theme Version:</span>
                <span>1.0.0</span>
              </div>
            </div>
            <div className="space-y-2">
              <div className="flex justify-between">
                <span className="text-muted-foreground">Memory Limit:</span>
                <span>256M</span>
              </div>
              <div className="flex justify-between">
                <span className="text-muted-foreground">Max Upload Size:</span>
                <span>64M</span>
              </div>
              <div className="flex justify-between">
                <span className="text-muted-foreground">Active Plugins:</span>
                <span>12</span>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Contact Support</CardTitle>
          <CardDescription>
            Need more help? Get in touch with our support team
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Button variant="outline" className="w-full">
              Open Support Ticket
            </Button>
            <Button variant="outline" className="w-full">
              Email Support
            </Button>
          </div>
          <p className="text-xs text-muted-foreground text-center">
            Support response time: 24-48 hours
          </p>
        </CardContent>
      </Card>
    </div>
  );
};
