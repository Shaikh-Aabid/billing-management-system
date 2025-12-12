<template>
    <div>
        <h1 class="text-h4 mb-6">Settings</h1>

        <v-row>
            <!-- Business Settings -->
            <v-col cols="12" md="6">
                <v-card>
                    <v-card-title>Business Information</v-card-title>
                    <v-card-text>
                        <v-form ref="businessForm" v-model="businessValid">
                            <v-text-field
                                v-model="businessSettings.business_name"
                                label="Business Name"
                                :rules="[v => !!v || 'Required']"
                            ></v-text-field>
                            <v-text-field
                                v-model="businessSettings.gstin"
                                label="GSTIN"
                                :rules="gstinRules"
                            ></v-text-field>
                            <v-textarea
                                v-model="businessSettings.address"
                                label="Business Address"
                                rows="3"
                                :rules="[v => !!v || 'Required']"
                            ></v-textarea>
                            <v-text-field
                                v-model="businessSettings.phone"
                                label="Phone Number"
                            ></v-text-field>
                            <v-text-field
                                v-model="businessSettings.email"
                                label="Email"
                                type="email"
                            ></v-text-field>
                            <v-text-field
                                v-model="businessSettings.state_code"
                                label="State Code"
                                hint="2-digit state code (e.g., 27 for Maharashtra)"
                                :rules="[v => !v || /^\d{2}$/.test(v) || 'Must be 2 digits']"
                            ></v-text-field>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primary"
                            :loading="savingBusiness"
                            :disabled="!businessValid"
                            @click="saveBusinessSettings"
                        >
                            Save Business Info
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>

            <!-- Account Settings -->
            <v-col cols="12" md="6">
                <v-card class="mb-4">
                    <v-card-title>Account Settings</v-card-title>
                    <v-card-text>
                        <v-form ref="accountForm" v-model="accountValid">
                            <v-text-field
                                v-model="accountSettings.name"
                                label="Full Name"
                                :rules="[v => !!v || 'Required']"
                            ></v-text-field>
                            <v-text-field
                                v-model="accountSettings.email"
                                label="Email"
                                type="email"
                                :rules="emailRules"
                            ></v-text-field>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primary"
                            :loading="savingAccount"
                            :disabled="!accountValid"
                            @click="saveAccountSettings"
                        >
                            Update Account
                        </v-btn>
                    </v-card-actions>
                </v-card>

                <!-- Change Password -->
                <v-card>
                    <v-card-title>Change Password</v-card-title>
                    <v-card-text>
                        <v-form ref="passwordForm" v-model="passwordValid">
                            <v-text-field
                                v-model="passwordData.current_password"
                                label="Current Password"
                                type="password"
                                :rules="[v => !!v || 'Required']"
                            ></v-text-field>
                            <v-text-field
                                v-model="passwordData.password"
                                label="New Password"
                                type="password"
                                :rules="passwordRules"
                            ></v-text-field>
                            <v-text-field
                                v-model="passwordData.password_confirmation"
                                label="Confirm New Password"
                                type="password"
                                :rules="confirmPasswordRules"
                            ></v-text-field>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="warning"
                            :loading="savingPassword"
                            :disabled="!passwordValid"
                            @click="changePassword"
                        >
                            Change Password
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>

        <!-- Bill Settings -->
        <v-row class="mt-4">
            <v-col cols="12" md="6">
                <v-card>
                    <v-card-title>Bill Settings</v-card-title>
                    <v-card-text>
                        <v-form ref="billForm" v-model="billSettingsValid">
                            <v-text-field
                                v-model="billSettings.bill_prefix"
                                label="Bill Number Prefix"
                                hint="e.g., INV, BILL"
                            ></v-text-field>
                            <v-text-field
                                v-model.number="billSettings.bill_start_number"
                                label="Starting Bill Number"
                                type="number"
                                min="1"
                            ></v-text-field>
                            <v-textarea
                                v-model="billSettings.terms_conditions"
                                label="Terms & Conditions"
                                rows="4"
                                hint="These will appear on your bills"
                            ></v-textarea>
                            <v-textarea
                                v-model="billSettings.bank_details"
                                label="Bank Details"
                                rows="3"
                                hint="Bank account details for payment"
                            ></v-textarea>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primary"
                            :loading="savingBillSettings"
                            @click="saveBillSettings"
                        >
                            Save Bill Settings
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>

            <!-- GST Rates -->
            <v-col cols="12" md="6">
                <v-card>
                    <v-card-title>Default GST Rates</v-card-title>
                    <v-card-text>
                        <v-alert type="info" variant="tonal" class="mb-4">
                            These are the standard GST rates in India. Products can have custom rates.
                        </v-alert>
                        <v-chip-group>
                            <v-chip
                                v-for="rate in gstRates"
                                :key="rate"
                                color="primary"
                                variant="outlined"
                            >
                                {{ rate }}%
                            </v-chip>
                        </v-chip-group>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const showSnackbar = inject('showSnackbar');

const businessForm = ref(null);
const accountForm = ref(null);
const passwordForm = ref(null);
const billForm = ref(null);

const businessValid = ref(false);
const accountValid = ref(false);
const passwordValid = ref(false);
const billSettingsValid = ref(false);

const savingBusiness = ref(false);
const savingAccount = ref(false);
const savingPassword = ref(false);
const savingBillSettings = ref(false);

const businessSettings = ref({
    business_name: '',
    gstin: '',
    address: '',
    phone: '',
    email: '',
    state_code: '',
});

const accountSettings = ref({
    name: '',
    email: '',
});

const passwordData = ref({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const billSettings = ref({
    bill_prefix: 'INV',
    bill_start_number: 1,
    terms_conditions: '',
    bank_details: '',
});

const gstRates = [0, 5, 12, 18, 28];

const gstinRules = [
    (v) => !v || v.length === 15 || 'GSTIN must be 15 characters',
    (v) => !v || /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/.test(v) || 'Invalid GSTIN format',
];

const emailRules = [
    (v) => !!v || 'Email is required',
    (v) => /.+@.+\..+/.test(v) || 'Email must be valid',
];

const passwordRules = [
    (v) => !!v || 'Password is required',
    (v) => v.length >= 8 || 'Password must be at least 8 characters',
];

const confirmPasswordRules = [
    (v) => !!v || 'Please confirm your password',
    (v) => v === passwordData.value.password || 'Passwords do not match',
];

const loadSettings = async () => {
    try {
        const response = await axios.get('/settings');
        businessSettings.value = { ...businessSettings.value, ...response.data.business };
        billSettings.value = { ...billSettings.value, ...response.data.bill };
        accountSettings.value = {
            name: authStore.user?.name || '',
            email: authStore.user?.email || '',
        };
    } catch (error) {
        console.error('Failed to load settings:', error);
    }
};

const saveBusinessSettings = async () => {
    savingBusiness.value = true;
    try {
        await axios.put('/settings/business', businessSettings.value);
        showSnackbar('Business settings saved!', 'success');
    } catch (error) {
        showSnackbar('Failed to save settings', 'error');
    } finally {
        savingBusiness.value = false;
    }
};

const saveAccountSettings = async () => {
    savingAccount.value = true;
    try {
        await axios.put('/settings/account', accountSettings.value);
        await authStore.fetchUser();
        showSnackbar('Account updated!', 'success');
    } catch (error) {
        showSnackbar('Failed to update account', 'error');
    } finally {
        savingAccount.value = false;
    }
};

const changePassword = async () => {
    savingPassword.value = true;
    try {
        await axios.put('/settings/password', passwordData.value);
        passwordData.value = {
            current_password: '',
            password: '',
            password_confirmation: '',
        };
        showSnackbar('Password changed successfully!', 'success');
    } catch (error) {
        showSnackbar(error.response?.data?.message || 'Failed to change password', 'error');
    } finally {
        savingPassword.value = false;
    }
};

const saveBillSettings = async () => {
    savingBillSettings.value = true;
    try {
        await axios.put('/settings/bill', billSettings.value);
        showSnackbar('Bill settings saved!', 'success');
    } catch (error) {
        showSnackbar('Failed to save bill settings', 'error');
    } finally {
        savingBillSettings.value = false;
    }
};

onMounted(() => {
    loadSettings();
});
</script>
