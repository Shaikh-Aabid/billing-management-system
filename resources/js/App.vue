<template>
    <v-app :theme="theme">
        <!-- Navigation Drawer -->
        <v-navigation-drawer
            v-if="isAuthenticated"
            v-model="drawer"
            :rail="!mobile && rail"
            :temporary="mobile"
            :permanent="!mobile"
            class="border-e"
        >
            <!-- Logo & Brand -->
            <div class="pa-4 d-flex align-center" v-if="!rail">
                <div class="rounded-lg bg-primary pa-2 mr-3">
                    <v-icon color="white" size="24">mdi-receipt-text</v-icon>
                </div>
                <div>
                    <div class="text-subtitle-1 font-weight-bold">BillFlow</div>
                    <div class="text-caption text-medium-emphasis">Billing System</div>
                </div>
            </div>
            <div class="pa-4 d-flex justify-center" v-else>
                <div class="rounded-lg bg-primary pa-2">
                    <v-icon color="white" size="20">mdi-receipt-text</v-icon>
                </div>
            </div>

            <v-divider class="mb-2"></v-divider>

            <!-- User Info -->
            <div class="px-4 py-3" v-if="!rail">
                <div class="d-flex align-center">
                    <v-avatar color="primary" size="40" class="mr-3">
                        <span class="text-white font-weight-medium">{{ userInitials }}</span>
                    </v-avatar>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="text-subtitle-2 font-weight-medium text-truncate">{{ user?.name }}</div>
                        <div class="text-caption text-medium-emphasis text-truncate">{{ user?.business_name }}</div>
                    </div>
                </div>
            </div>
            <div class="px-2 py-3 d-flex justify-center" v-else>
                <v-avatar color="primary" size="36">
                    <span class="text-white text-caption font-weight-medium">{{ userInitials }}</span>
                </v-avatar>
            </div>

            <v-divider class="mb-2"></v-divider>

            <!-- Navigation -->
            <v-list density="comfortable" nav class="px-2">
                <v-list-item
                    v-for="item in navItems"
                    :key="item.title"
                    :prepend-icon="item.icon"
                    :title="item.title"
                    :to="item.to"
                    :value="item.title"
                    color="primary"
                    rounded="lg"
                    class="mb-1"
                >
                </v-list-item>
            </v-list>

            <template v-slot:append>
                <v-divider></v-divider>
                <div class="pa-3">
                    <v-btn
                        block
                        variant="tonal"
                        color="error"
                        :prepend-icon="rail ? '' : 'mdi-logout'"
                        :icon="rail ? 'mdi-logout' : undefined"
                        @click="handleLogout"
                        :loading="authStore.loading"
                        rounded="lg"
                    >
                        <span v-if="!rail">Logout</span>
                    </v-btn>
                </div>
            </template>
        </v-navigation-drawer>

        <!-- App Bar -->
        <v-app-bar v-if="isAuthenticated" flat class="border-b" color="surface">
            <v-btn
                icon
                variant="text"
                @click="toggleDrawer"
                class="ml-2"
            >
                <v-icon>{{ (!mobile && rail) || (mobile && !drawer) ? 'mdi-menu' : 'mdi-menu-open' }}</v-icon>
            </v-btn>

            <v-breadcrumbs :items="breadcrumbs" class="text-body-2">
                <template v-slot:divider>
                    <v-icon size="small">mdi-chevron-right</v-icon>
                </template>
            </v-breadcrumbs>

            <v-spacer></v-spacer>

            <!-- Search -->
            <v-text-field
                v-if="$vuetify.display.mdAndUp"
                placeholder="Search..."
                prepend-inner-icon="mdi-magnify"
                density="compact"
                variant="outlined"
                hide-details
                single-line
                class="mr-4"
                style="max-width: 280px;"
            ></v-text-field>

            <!-- Theme Toggle -->
            <v-btn
                icon
                variant="text"
                @click="toggleTheme"
                class="mr-1"
            >
                <v-icon>{{ theme === 'light' ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
            </v-btn>

            <!-- Notifications -->
            <v-btn icon variant="text" class="mr-1">
                <v-badge color="error" content="3" overlap>
                    <v-icon>mdi-bell-outline</v-icon>
                </v-badge>
            </v-btn>

            <!-- Profile Menu -->
            <v-menu>
                <template v-slot:activator="{ props }">
                    <v-btn
                        v-bind="props"
                        variant="text"
                        class="mr-2"
                    >
                        <v-avatar color="primary" size="32" class="mr-2">
                            <span class="text-white text-caption">{{ userInitials }}</span>
                        </v-avatar>
                        <span class="d-none d-md-inline">{{ user?.name }}</span>
                        <v-icon end>mdi-chevron-down</v-icon>
                    </v-btn>
                </template>
                <v-list rounded="lg" min-width="200">
                    <v-list-item prepend-icon="mdi-account-outline" title="Profile" to="/settings"></v-list-item>
                    <v-list-item prepend-icon="mdi-cog-outline" title="Settings" to="/settings"></v-list-item>
                    <v-divider class="my-1"></v-divider>
                    <v-list-item prepend-icon="mdi-logout" title="Logout" @click="handleLogout" base-color="error"></v-list-item>
                </v-list>
            </v-menu>
        </v-app-bar>

        <!-- Main Content -->
        <v-main class="bg-background">
            <v-container fluid :class="mobile ? 'pa-4' : 'pa-6'">
                <router-view v-slot="{ Component }">
                    <transition name="fade" mode="out-in">
                        <component :is="Component" />
                    </transition>
                </router-view>
            </v-container>
        </v-main>

        <!-- Snackbar for notifications -->
        <v-snackbar
            v-model="snackbar.show"
            :color="snackbar.color"
            :timeout="3000"
            location="top right"
            rounded="lg"
        >
            <div class="d-flex align-center">
                <v-icon class="mr-2">
                    {{ snackbar.color === 'success' ? 'mdi-check-circle' : snackbar.color === 'error' ? 'mdi-alert-circle' : 'mdi-information' }}
                </v-icon>
                {{ snackbar.text }}
            </div>
            <template v-slot:actions>
                <v-btn variant="text" @click="snackbar.show = false" icon="mdi-close" size="small"></v-btn>
            </template>
        </v-snackbar>
    </v-app>
</template>

<script setup>
import { ref, computed, provide, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from './stores/auth';

import { useDisplay } from 'vuetify';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const { mobile } = useDisplay();

const drawer = ref(!mobile.value);
const rail = ref(false);
const theme = ref(localStorage.getItem('theme') || 'light');

// Sync drawer state with screen size
watch(mobile, (val) => {
    drawer.value = !val;
    if (val) rail.value = false;
});

const toggleDrawer = () => {
    if (mobile.value) {
        drawer.value = !drawer.value;
    } else {
        rail.value = !rail.value;
    }
};

const snackbar = ref({
    show: false,
    text: '',
    color: 'success',
});

const showSnackbar = (text, color = 'success') => {
    snackbar.value = { show: true, text, color };
};

provide('showSnackbar', showSnackbar);

const isAuthenticated = computed(() => authStore.isAuthenticated);
const user = computed(() => authStore.user);

const userInitials = computed(() => {
    if (!user.value?.name) return 'U';
    return user.value.name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
});

const breadcrumbs = computed(() => {
    const items = [{ title: 'Home', disabled: false, to: '/' }];
    const path = route.path;

    if (path.startsWith('/parties')) items.push({ title: 'Parties', disabled: true });
    else if (path.startsWith('/products')) items.push({ title: 'Products', disabled: true });
    else if (path.startsWith('/bills/create')) items.push({ title: 'Bills', disabled: false, to: '/bills' }, { title: 'Create', disabled: true });
    else if (path.startsWith('/bills/')) items.push({ title: 'Bills', disabled: false, to: '/bills' }, { title: 'Details', disabled: true });
    else if (path.startsWith('/bills')) items.push({ title: 'Bills', disabled: true });
    else if (path.startsWith('/eway-bills')) items.push({ title: 'E-Way Bills', disabled: true });
    else if (path.startsWith('/settings')) items.push({ title: 'Settings', disabled: true });
    else if (path === '/') items[0].title = 'Dashboard';

    return items;
});

const navItems = [
    { title: 'Dashboard', icon: 'mdi-view-dashboard-outline', to: '/' },
    { title: 'Parties', icon: 'mdi-account-group-outline', to: '/parties' },
    { title: 'Products', icon: 'mdi-package-variant', to: '/products' },
    { title: 'Bills', icon: 'mdi-receipt-text-outline', to: '/bills' },
    { title: 'E-Way Bills', icon: 'mdi-truck-outline', to: '/eway-bills' },
    { title: 'Settings', icon: 'mdi-cog-outline', to: '/settings' },
];

const toggleTheme = () => {
    theme.value = theme.value === 'light' ? 'dark' : 'light';
    localStorage.setItem('theme', theme.value);
};

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>

<style>
html {
    overflow-y: auto;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.v-navigation-drawer {
    background: rgb(var(--v-theme-surface)) !important;
}

.v-list-item--active {
    background: rgba(var(--v-theme-primary), 0.1) !important;
}

.border {
    border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity)) !important;
}

.border-e {
    border-right: 1px solid rgba(var(--v-border-color), var(--v-border-opacity)) !important;
}

.border-b {
    border-bottom: 1px solid rgba(var(--v-border-color), var(--v-border-opacity)) !important;
}

/* Custom Tooltip Styles for better visibility */
.custom-tooltip {
    background-color: #1E293B !important;
    color: #FFFFFF !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    padding: 8px 12px !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
    opacity: 1 !important;
}

.v-theme--dark .custom-tooltip {
    background-color: #F1F5F9 !important;
    color: #1E293B !important;
}

/* Fallback for default tooltips */
.v-tooltip > .v-overlay__content {
    background-color: #1E293B !important;
    color: #FFFFFF !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    padding: 8px 12px !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
    opacity: 1 !important;
}

.v-theme--dark .v-tooltip > .v-overlay__content {
    background-color: #F1F5F9 !important;
    color: #1E293B !important;
}
</style>
