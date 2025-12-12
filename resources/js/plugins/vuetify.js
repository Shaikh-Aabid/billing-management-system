import 'vuetify/styles';
import '@mdi/font/css/materialdesignicons.css';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';

const vuetify = createVuetify({
    components,
    directives,
    theme: {
        defaultTheme: 'light',
        themes: {
            light: {
                dark: false,
                colors: {
                    background: '#F8FAFC',
                    surface: '#FFFFFF',
                    'surface-variant': '#F1F5F9',
                    primary: '#6366F1',
                    'primary-darken-1': '#4F46E5',
                    secondary: '#8B5CF6',
                    'secondary-darken-1': '#7C3AED',
                    accent: '#EC4899',
                    error: '#EF4444',
                    info: '#3B82F6',
                    success: '#10B981',
                    warning: '#F59E0B',
                    'on-background': '#1E293B',
                    'on-surface': '#334155',
                },
            },
            dark: {
                dark: true,
                colors: {
                    background: '#0F172A',
                    surface: '#1E293B',
                    'surface-variant': '#334155',
                    primary: '#818CF8',
                    'primary-darken-1': '#6366F1',
                    secondary: '#A78BFA',
                    'secondary-darken-1': '#8B5CF6',
                    accent: '#F472B6',
                    error: '#F87171',
                    info: '#60A5FA',
                    success: '#34D399',
                    warning: '#FBBF24',
                },
            },
        },
    },
    defaults: {
        VCard: {
            elevation: 0,
            rounded: 'xl',
            class: 'border',
        },
        VBtn: {
            rounded: 'lg',
            elevation: 0,
        },
        VTextField: {
            variant: 'outlined',
            density: 'comfortable',
            rounded: 'lg',
        },
        VSelect: {
            variant: 'outlined',
            density: 'comfortable',
            rounded: 'lg',
        },
        VAutocomplete: {
            variant: 'outlined',
            density: 'comfortable',
            rounded: 'lg',
        },
        VTextarea: {
            variant: 'outlined',
            density: 'comfortable',
            rounded: 'lg',
        },
        VChip: {
            rounded: 'lg',
        },
        VAlert: {
            rounded: 'lg',
        },
        VDialog: {
            rounded: 'xl',
        },
        VTooltip: {
            contentClass: 'custom-tooltip',
        },
    },
});

export default vuetify;
