import useSWR from 'swr';
import { settingsAPI } from '@/admin/services/api';

interface Language {
  name: string;
  code: string;
  flag: string;
  country: string;
  is_default: boolean;
  active: boolean;
}

interface LanguagesResponse {
  success: boolean;
  languages: Record<string, Language>;
  switcher_enabled: boolean;
}

export const useLanguages = () => {
  const { data, error, mutate } = useSWR<LanguagesResponse>(
    '/gst/v1/languages',
    () => settingsAPI.getLanguages(),
    {
      revalidateOnFocus: false,
      revalidateOnReconnect: false,
      dedupingInterval: 60000, // 1 minute
    }
  );

  const saveLanguages = async (languages: Record<string, Language>, switcherEnabled: boolean) => {
    try {
      const result = await settingsAPI.saveLanguages(languages, switcherEnabled);
      if (result) {
        // Optimistically update the cache
        mutate({
          success: true,
          languages,
          switcher_enabled: switcherEnabled
        }, false);
      }
      return result;
    } catch (error) {
      console.error('Error saving languages:', error);
      throw error;
    }
  };

  return {
    languages: data?.languages || {},
    switcherEnabled: data?.switcher_enabled || false,
    loading: !data && !error,
    error,
    saveLanguages,
    mutate
  };
};
