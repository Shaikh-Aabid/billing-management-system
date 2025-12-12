<template>
    <v-container class="fill-height register-container" fluid>
        <v-row align="center" justify="center" class="fill-height">
            <v-col cols="12" sm="10" md="8" lg="7">
                <v-row align="center" justify="center">
                    <!-- Left Side - Branding -->
                    <v-col cols="12" md="5" class="d-none d-md-flex flex-column align-center justify-center pa-8">
                        <div class="text-center">
                            <div class="rounded-xl bg-primary pa-4 mb-6 d-inline-block">
                                <v-icon color="white" size="64">mdi-receipt-text</v-icon>
                            </div>
                            <h1 class="text-h3 font-weight-bold text-primary mb-3">BillFlow</h1>
                            <p class="text-body-1 text-medium-emphasis">
                                Join thousands of businesses managing their billing with ease.
                            </p>
                            <div class="mt-8">
                                <div class="d-flex align-center justify-center mb-3">
                                    <v-icon color="success" class="mr-2">mdi-check-circle</v-icon>
                                    <span class="text-body-2">Free to get started</span>
                                </div>
                                <div class="d-flex align-center justify-center mb-3">
                                    <v-icon color="success" class="mr-2">mdi-check-circle</v-icon>
                                    <span class="text-body-2">Unlimited invoices</span>
                                </div>
                                <div class="d-flex align-center justify-center">
                                    <v-icon color="success" class="mr-2">mdi-check-circle</v-icon>
                                    <span class="text-body-2">GST compliant</span>
                                </div>
                            </div>
                        </div>
                    </v-col>

                    <!-- Right Side - Registration Form -->
                    <v-col cols="12" md="7">
                        <v-card class="pa-6 pa-md-8 register-card">
                            <!-- Mobile Logo -->
                            <div class="d-md-none text-center mb-6">
                                <div class="rounded-lg bg-primary pa-3 d-inline-block mb-3">
                                    <v-icon color="white" size="32">mdi-receipt-text</v-icon>
                                </div>
                                <h2 class="text-h5 font-weight-bold text-primary">BillFlow</h2>
                            </div>

                            <div class="text-center mb-6">
                                <h2 class="text-h5 font-weight-bold mb-2">Create Account</h2>
                                <p class="text-body-2 text-medium-emphasis">Start managing your business billing today</p>
                            </div>

                            <v-form ref="form" v-model="valid" @submit.prevent="handleRegister">
                                <v-row>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="userData.name"
                                            label="Full Name"
                                            placeholder="John Doe"
                                            prepend-inner-icon="mdi-account-outline"
                                            :rules="nameRules"
                                            variant="outlined"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="userData.email"
                                            label="Email Address"
                                            placeholder="you@example.com"
                                            prepend-inner-icon="mdi-email-outline"
                                            type="email"
                                            :rules="emailRules"
                                            variant="outlined"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="userData.business_name"
                                            label="Business Name"
                                            placeholder="Your Company Pvt Ltd"
                                            prepend-inner-icon="mdi-domain"
                                            :rules="businessNameRules"
                                            variant="outlined"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="userData.gstin"
                                            label="GSTIN (Optional)"
                                            placeholder="27AAPFU0939F1ZV"
                                            prepend-inner-icon="mdi-card-account-details-outline"
                                            :rules="gstinRules"
                                            variant="outlined"
                                            hint="15-character GST Identification Number"
                                            persistent-hint
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="userData.password"
                                            label="Password"
                                            placeholder="Min 8 characters"
                                            prepend-inner-icon="mdi-lock-outline"
                                            :type="showPassword ? 'text' : 'password'"
                                            :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                                            @click:append-inner="showPassword = !showPassword"
                                            :rules="passwordRules"
                                            variant="outlined"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="userData.password_confirmation"
                                            label="Confirm Password"
                                            placeholder="Re-enter password"
                                            prepend-inner-icon="mdi-lock-check-outline"
                                            :type="showPassword ? 'text' : 'password'"
                                            :rules="confirmPasswordRules"
                                            variant="outlined"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                </v-row>

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
                                    @click="handleRegister"
                                    class="mb-4"
                                >
                                    <v-icon start>mdi-account-plus</v-icon>
                                    Create Account
                                </v-btn>

                                <div class="text-center">
                                    <span class="text-body-2 text-medium-emphasis">Already have an account?</span>
                                    <router-link to="/login" class="text-primary font-weight-medium text-decoration-none ml-1">
                                        Sign in
                                    </router-link>
                                </div>
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

const userData = ref({
    name: '',
    email: '',
    business_name: '',
    gstin: '',
    password: '',
    password_confirmation: '',
});

const nameRules = [
    (v) => !!v || 'Name is required',
    (v) => v.length >= 2 || 'Name must be at least 2 characters',
];

const emailRules = [
    (v) => !!v || 'Email is required',
    (v) => /.+@.+\..+/.test(v) || 'Email must be valid',
];

const businessNameRules = [
    (v) => !!v || 'Business name is required',
];

const gstinRules = [
    (v) => !v || v.length === 15 || 'GSTIN must be 15 characters',
    (v) => !v || /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/.test(v) || 'Invalid GSTIN format',
];

const passwordRules = [
    (v) => !!v || 'Password is required',
    (v) => v.length >= 8 || 'Password must be at least 8 characters',
];

const confirmPasswordRules = [
    (v) => !!v || 'Please confirm your password',
    (v) => v === userData.value.password || 'Passwords do not match',
];

const handleRegister = async () => {
    if (!valid.value) return;

    loading.value = true;
    error.value = '';

    try {
        await authStore.register(userData.value);
        router.push('/');
    } catch (err) {
        error.value = err.response?.data?.message || 'Registration failed. Please try again.';
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.register-container {
    background: linear-gradient(135deg, #F8FAFC 0%, #EEF2FF 50%, #F1F5F9 100%);
    min-height: 100vh;
}

.register-card {
    border: 1px solid rgba(99, 102, 241, 0.1) !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 10px 15px -3px rgba(99, 102, 241, 0.1) !important;
}
</style>
