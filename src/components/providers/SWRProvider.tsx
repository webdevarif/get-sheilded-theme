import React from 'react';
import { SWRConfig } from 'swr';
import { adminSwrConfig } from '../../lib/swr-config';

interface SWRProviderProps {
  children: React.ReactNode;
}

/**
 * SWR Provider component
 * Wraps the application with SWR configuration
 */
export const SWRProvider: React.FC<SWRProviderProps> = ({ children }) => {
  return (
    <SWRConfig value={adminSwrConfig}>
      {children}
    </SWRConfig>
  );
};
