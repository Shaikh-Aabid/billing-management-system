<template>
    <v-dialog v-model="dialog" max-width="600" persistent>
        <v-card>
            <v-card-title>
                {{ isEdit ? 'Edit Party' : 'Add New Party' }}
            </v-card-title>
            <v-card-text>
                <v-form ref="form" v-model="valid">
                    <v-text-field
                        v-model="formData.name"
                        label="Party Name"
                        :rules="[v => !!v || 'Name is required']"
                        autofocus
                    ></v-text-field>
                    <v-text-field
                        v-model="formData.gstin"
                        label="GSTIN"
                        :rules="gstinRules"
                        hint="15-character GST Identification Number"
                    ></v-text-field>
                    <v-textarea
                        v-model="formData.address"
                        label="Address"
                        rows="3"
                        :rules="[v => !!v || 'Address is required']"
                    ></v-textarea>
                    <v-row>
                        <v-col cols="6">
                            <v-text-field
                                v-model="formData.city"
                                label="City"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                v-model="formData.state"
                                label="State"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="6">
                            <v-text-field
                                v-model="formData.pincode"
                                label="Pincode"
                                :rules="pincodeRules"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                v-model="formData.contact_number"
                                label="Contact Number"
                                :rules="phoneRules"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-text-field
                        v-model="formData.email"
                        label="Email"
                        type="email"
                        :rules="emailRules"
                    ></v-text-field>
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn variant="text" @click="close">Cancel</v-btn>
                <v-btn
                    color="primary"
                    :loading="loading"
                    :disabled="!valid"
                    @click="save"
                >
                    {{ isEdit ? 'Update' : 'Create' }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { usePartyStore } from '../stores/party';

const props = defineProps({
    modelValue: Boolean,
    party: Object,
});

const emit = defineEmits(['update:modelValue', 'saved']);

const partyStore = usePartyStore();

const form = ref(null);
const valid = ref(false);
const loading = ref(false);

const dialog = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const isEdit = computed(() => !!props.party);

const formData = ref({
    name: '',
    gstin: '',
    address: '',
    city: '',
    state: '',
    pincode: '',
    contact_number: '',
    email: '',
});

const gstinRules = [
    (v) => !v || v.length === 15 || 'GSTIN must be 15 characters',
    (v) => !v || /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/.test(v) || 'Invalid GSTIN format',
];

const pincodeRules = [
    (v) => !v || /^\d{6}$/.test(v) || 'Pincode must be 6 digits',
];

const phoneRules = [
    (v) => !v || /^\d{10}$/.test(v) || 'Phone must be 10 digits',
];

const emailRules = [
    (v) => !v || /.+@.+\..+/.test(v) || 'Email must be valid',
];

watch(() => props.modelValue, (newVal) => {
    if (newVal) {
        if (props.party) {
            formData.value = { ...props.party };
        } else {
            resetForm();
        }
    }
});

const resetForm = () => {
    formData.value = {
        name: '',
        gstin: '',
        address: '',
        city: '',
        state: '',
        pincode: '',
        contact_number: '',
        email: '',
    };
};

const close = () => {
    dialog.value = false;
    resetForm();
};

const save = async () => {
    if (!valid.value) return;

    loading.value = true;
    try {
        let savedParty;
        if (isEdit.value) {
            savedParty = await partyStore.updateParty(props.party.id, formData.value);
        } else {
            savedParty = await partyStore.createParty(formData.value);
        }
        emit('saved', savedParty);
        close();
    } catch (error) {
        console.error('Failed to save party:', error);
    } finally {
        loading.value = false;
    }
};
</script>
