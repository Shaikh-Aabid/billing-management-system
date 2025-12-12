<template>
    <v-dialog v-model="dialog" max-width="600" persistent>
        <v-card>
            <v-card-title>Generate E-Way Bill</v-card-title>
            <v-card-text>
                <v-alert type="info" variant="tonal" class="mb-4">
                    E-Way Bill is required for transporting goods worth more than ₹50,000.
                </v-alert>
                <v-form ref="form" v-model="valid">
                    <v-select
                        v-model="formData.transport_mode"
                        :items="transportModes"
                        label="Transport Mode"
                        :rules="[v => !!v || 'Required']"
                    ></v-select>
                    <v-text-field
                        v-model="formData.vehicle_number"
                        label="Vehicle Number"
                        :rules="vehicleRules"
                        hint="e.g., MH12AB1234"
                        placeholder="XX00XX0000"
                    ></v-text-field>
                    <v-row>
                        <v-col cols="6">
                            <v-text-field
                                v-model="formData.transporter_id"
                                label="Transporter ID (GSTIN)"
                                :rules="gstinRules"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                v-model="formData.transporter_name"
                                label="Transporter Name"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="6">
                            <v-text-field
                                v-model.number="formData.distance"
                                label="Distance (km)"
                                type="number"
                                min="1"
                                :rules="[v => v > 0 || 'Distance is required']"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-select
                                v-model="formData.vehicle_type"
                                :items="vehicleTypes"
                                label="Vehicle Type"
                            ></v-select>
                        </v-col>
                    </v-row>
                    <v-textarea
                        v-model="formData.from_address"
                        label="From Address"
                        rows="2"
                        :rules="[v => !!v || 'Required']"
                    ></v-textarea>
                    <v-textarea
                        v-model="formData.to_address"
                        label="To Address"
                        rows="2"
                        :rules="[v => !!v || 'Required']"
                    ></v-textarea>
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn variant="text" @click="close">Cancel</v-btn>
                <v-btn
                    color="primary"
                    :loading="loading"
                    :disabled="!valid"
                    @click="generate"
                >
                    Generate E-Way Bill
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useBillStore } from '../stores/bill';

const props = defineProps({
    modelValue: Boolean,
    bill: Object,
});

const emit = defineEmits(['update:modelValue', 'generated']);

const billStore = useBillStore();

const form = ref(null);
const valid = ref(false);
const loading = ref(false);

const dialog = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const formData = ref({
    transport_mode: 'Road',
    vehicle_number: '',
    transporter_id: '',
    transporter_name: '',
    distance: null,
    vehicle_type: 'Regular',
    from_address: '',
    to_address: '',
});

const transportModes = [
    'Road',
    'Rail',
    'Air',
    'Ship',
];

const vehicleTypes = [
    'Regular',
    'ODC (Over Dimensional Cargo)',
];

const vehicleRules = [
    (v) => !!v || 'Vehicle number is required',
    (v) => /^[A-Z]{2}\d{2}[A-Z]{1,2}\d{4}$/.test(v?.toUpperCase()) || 'Invalid vehicle number format',
];

const gstinRules = [
    (v) => !v || v.length === 15 || 'GSTIN must be 15 characters',
    (v) => !v || /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/.test(v) || 'Invalid GSTIN format',
];

watch(() => props.modelValue, (newVal) => {
    if (newVal && props.bill) {
        // Pre-fill addresses from party
        formData.value.to_address = props.bill.party?.address || '';
    }
});

const close = () => {
    dialog.value = false;
    resetForm();
};

const resetForm = () => {
    formData.value = {
        transport_mode: 'Road',
        vehicle_number: '',
        transporter_id: '',
        transporter_name: '',
        distance: null,
        vehicle_type: 'Regular',
        from_address: '',
        to_address: '',
    };
};

const generate = async () => {
    if (!valid.value || !props.bill) return;

    loading.value = true;
    try {
        // Convert vehicle number to uppercase
        formData.value.vehicle_number = formData.value.vehicle_number.toUpperCase();

        await billStore.generateEwayBill(props.bill.id, formData.value);
        emit('generated');
        close();
    } catch (error) {
        console.error('Failed to generate e-way bill:', error);
    } finally {
        loading.value = false;
    }
};
</script>
