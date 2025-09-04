import { SWRConfiguration } from 'swr';

/**
 * Global SWR configuration
 * This provides default settings for all SWR hooks across the application
 */
export const swrConfig: SWRConfiguration = {
  // Revalidate on window focus
  revalidateOnFocus: false,
  
  // Revalidate when reconnecting to the network
  revalidateOnReconnect: true,
  
  // Don't revalidate on mount if data exists
  revalidateIfStale: true,
  
  // Dedupe requests within this time window (in ms)
  dedupingInterval: 2000,
  
  // Error retry configuration
  errorRetryCount: 3,
  errorRetryInterval: 5000,
  
  // Loading timeout
  loadingTimeout: 10000,
  
  // Global error handler
  onError: (error, key) => {
    console.error('SWR Error:', error, 'for key:', key);
  },
  
  // Global loading handler
  onLoadingSlow: (key, config) => {
    console.warn('SWR Loading slow for key:', key);
  },
  
  // Global success handler
  onSuccess: (data, key) => {
    console.log('SWR Success for key:', key, data);
  },
};

/**
 * SWR configuration for admin API calls
 * More conservative settings for admin operations
 */
export const adminSwrConfig: SWRConfiguration = {
  ...swrConfig,
  
  // More frequent revalidation for admin
  revalidateOnFocus: true,
  
  // Shorter deduping interval for admin
  dedupingInterval: 1000,
  
  // More retries for admin operations
  errorRetryCount: 5,
  
  // Longer timeout for admin operations
  loadingTimeout: 15000,
};
