/**
 * Centralized API configuration
 * All API endpoints and base URLs are defined here for easy maintenance
 */

// Base API configuration
export const API_CONFIG = {
  BASE_URL: '/wp-json/gst/v1',
  NONCE_HEADER: 'X-WP-Nonce',
  CONTENT_TYPE: 'application/json',
} as const;

// Language API endpoints
export const LANGUAGE_ENDPOINTS = {
  GET_LANGUAGES: `${API_CONFIG.BASE_URL}/languages`,
  SAVE_LANGUAGES: `${API_CONFIG.BASE_URL}/languages`,
  SAVE_SWITCHER_STATE: `${API_CONFIG.BASE_URL}/languages/switcher`,
  ADD_LANGUAGE: `${API_CONFIG.BASE_URL}/languages/add`,
  REMOVE_LANGUAGE: `${API_CONFIG.BASE_URL}/languages/remove`,
  SET_DEFAULT_LANGUAGE: `${API_CONFIG.BASE_URL}/languages/default`,
} as const;

// Common headers for API requests
export const getApiHeaders = (nonce?: string) => ({
  'Content-Type': API_CONFIG.CONTENT_TYPE,
  [API_CONFIG.NONCE_HEADER]: nonce || (window as any).gstAdminData?.nonce || '',
});

// API response types
export interface ApiResponse<T = any> {
  success: boolean;
  message?: string;
  data?: T;
}

// Language data types
export interface LanguageData {
  name: string;
  flag: string;
  prefix: string;
  is_default: boolean;
  active: boolean;
}

export interface LanguageSettingsData {
  _enabled: boolean;
  [key: string]: LanguageData | boolean;
}

// API request types
export interface SaveLanguagesRequest {
  languages: string; // JSON string
}

export interface AddLanguageRequest {
  code: string;
  name: string;
  flag: string;
  prefix: string;
}

export interface RemoveLanguageRequest {
  code: string;
}

export interface SetDefaultLanguageRequest {
  code: string;
}
