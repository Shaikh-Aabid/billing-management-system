<template>
    <v-container class="fill-height login-container" fluid>
        <v-row align="center" justify="center" class="fill-height">
            <v-col cols="12" sm="10" md="8" lg="6">
                <v-row align="center" justify="center">
                    <!-- Left Side - Branding -->
                    <v-col cols="12" md="6" class="d-none d-md-flex flex-column align-center justify-center pa-8">
                        <div class="text-center">
                            <div class="rounded-xl bg-primary pa-4 mb-6 d-inline-block">
                                <v-icon color="white" size="64">mdi-receipt-text</v-icon>
                            </div>
                            <h1 class="text-h3 font-weight-bold text-primary mb-3">BillFlow</h1>
                            <p class="text-body-1 text-medium-emphasis">
                                Streamline your billing process with our modern GST-compliant invoicing system.
                            </p>
                            <div class="mt-8">
                                <div class="d-flex align-center justify-center mb-3">
                                    <v-icon color="success" class="mr-2">mdi-check-circle</v-icon>
                                    <span class="text-body-2">GST Compliant Invoices</span>
                                </div>
                                <div class="d-flex align-center justify-center mb-3">
                                    <v-icon color="success" class="mr-2">mdi-check-circle</v-icon>
                                    <span class="text-body-2">E-Way Bill Generation</span>
                                </div>
                                <div class="d-flex align-center justify-center">
                                    <v-icon color="success" class="mr-2">mdi-check-circle</v-icon>
                                    <span class="text-body-2">PDF Export & Analytics</span>
                                </div>
                            </div>

                        </div>
                    </v-col>

                    <!-- Right Side - Login Form -->
                    <v-col cols="12" md="6">
                        <v-card class="pa-6 pa-md-8 login-card">
                            <!-- Mobile Logo -->
                            <div class="d-md-none text-center mb-6">
                                <div class="rounded-lg bg-primary pa-3 d-inline-block mb-3">
                                    <v-icon color="white" size="32">mdi-receipt-text</v-icon>
                                </div>
                                <h2 class="text-h5 font-weight-bold text-primary">BillFlow</h2>
                            </div>

                            <div class="text-center mb-6">
                                <h2 class="text-h5 font-weight-bold mb-2">Welcome Back</h2>
                                <p class="text-body-2 text-medium-emphasis">Sign in to continue to your dashboard</p>
                            </div>

                            <v-form ref="form" v-model="valid" @submit.prevent="handleLogin">
                                <v-text-field
                                    v-model="credentials.email"
                                    label="Email Address"
                                    placeholder="Enter your email"
                                    prepend-inner-icon="mdi-email-outline"
                                    type="email"
                                    :rules="emailRules"
                                    variant="outlined"
                                    class="mb-4"
                                    required
                                ></v-text-field>

                                <v-text-field
                                    v-model="credentials.password"
                                    label="Password"
                                    placeholder="Enter your password"
                                    prepend-inner-icon="mdi-lock-outline"
                                    :type="showPassword ? 'text' : 'password'"
                                    :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                                    @click:append-inner="showPassword = !showPassword"
                                    :rules="passwordRules"
                                    variant="outlined"
                                    class="mb-2"
                                    required
                                ></v-text-field>

                                <div class="d-flex justify-space-between align-center mb-6">
                                    <v-checkbox
                                        label="Remember me"
                                        hide-details
                                        density="compact"
                                        color="primary"
                                    ></v-checkbox>
                                    <a href="#" class="text-primary text-body-2 text-decoration-none">Forgot password?</a>
                                </div>

                                <v-alert
                                    v-if="error"
                                    type="error"
                                    variant="tonal"
                                    class="mb-4"
                                    closable
                                    @click:close="error = ''"
                                    rounded="lg"
                                >
                                    {{ error }}
                                </v-alert>

                                <v-btn
                                    color="primary"
                                    size="large"
                                    block
                                    :loading="loading"
                                    :disabled="!valid"
                                    @click="handleLogin"
                                    class="mb-4"
                                >
                                    <v-icon start>mdi-login</v-icon>
                                    Sign In
                                </v-btn>

                                <div class="text-center">
                                    <span class="text-body-2 text-medium-emphasis">Don't have an account?</span>
                                    <router-link to="/register" class="text-primary font-weight-medium text-decoration-none ml-1">
                                        Create one
                                    </router-link>
                                </div>

                                <v-divider class="my-4"></v-divider>


                            </v-form>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = ref(null);
const valid = ref(false);
const showPassword = ref(false);
const loading = ref(false);
const error = ref('');

const credentials = ref({
    email: '',
    password: '',
});

const emailRules = [
    (v) => !!v || 'Email is required',
    (v) => /.+@.+\..+/.test(v) || 'Email must be valid',
];

const passwordRules = [
    (v) => !!v || 'Password is required',
    (v) => v.length >= 6 || 'Password must be at least 6 characters',
];

const handleLogin = async () => {
    if (!valid.value) return;

    loading.value = true;
    error.value = '';

    try {
        await authStore.login(credentials.value);
        router.push('/');
    } catch (err) {
        error.value = err.response?.data?.message || 'Login failed. Please try again.';
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.login-container {
    background: linear-gradient(135deg, rgba(var(--v-theme-primary), 0.1) 0%, rgba(var(--v-theme-secondary), 0.1) 100%), var(--v-theme-background);
    min-height: 100vh;
}

.login-card {
    border-radius: 24px !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important;
    background: rgba(var(--v-theme-surface), 0.95) !important;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(var(--v-theme-on-surface), 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.login-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.2) !important;
}

.v-theme--dark .login-card {
    background: rgba(var(--v-theme-surface), 0.8) !important;
    border: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
}
</style>
