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
                    background: '#F9FAFB',
                    surface: '#FFFFFF',
                    'surface-variant': '#F3F4F6',
                    primary: '#4F46E5', // Indigo
                    'primary-darken-1': '#4338CA',
                    secondary: '#10B981', // Emerald
                    'secondary-darken-1': '#059669',
                    accent: '#8B5CF6',
                    error: '#EF4444',
                    info: '#3B82F6',
                    success: '#10B981',
                    warning: '#F59E0B',
                    'on-background': '#111827',
                    'on-surface': '#1F2937',
                },
            },
            dark: {
                dark: true,
                colors: {
                    background: '#09090b', // Zinc 950
                    surface: '#18181b', // Zinc 900
                    'surface-variant': '#27272a',
                    primary: '#6366F1', // Indigo 500
                    'primary-darken-1': '#4F46E5',
                    secondary: '#34D399', // Emerald 400
                    'secondary-darken-1': '#10B981',
                    accent: '#A78BFA',
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
            elevation: 2,
            rounded: 'xl',
            class: 'card-hover',
        },
        VBtn: {
            rounded: 'pill',
            elevation: 0,
            class: 'btn-hover',
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
