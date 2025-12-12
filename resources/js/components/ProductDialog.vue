<template>
    <v-dialog v-model="dialog" max-width="600" persistent>
        <v-card>
            <v-card-title>
                {{ isEdit ? 'Edit Product' : 'Add New Product' }}
            </v-card-title>
            <v-card-text>
                <v-form ref="form" v-model="valid">
                    <v-text-field
                        v-model="formData.name"
                        label="Product Name"
                        :rules="[v => !!v || 'Name is required']"
                        autofocus
                    ></v-text-field>
                    <v-textarea
                        v-model="formData.description"
                        label="Description"
                        rows="2"
                    ></v-textarea>
                    <v-row>
                        <v-col cols="6">
                            <v-text-field
                                v-model="formData.hsn_code"
                                label="HSN Code"
                                :rules="hsnRules"
                                hint="4-8 digit HSN/SAC code"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-select
                                v-model="formData.unit"
                                :items="units"
                                label="Unit"
                                :rules="[v => !!v || 'Unit is required']"
                            ></v-select>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="6">
                            <v-text-field
                                v-model.number="formData.price"
                                label="Price"
                                type="number"
                                min="0"
                                step="0.01"
                                prefix="₹"
                                :rules="[v => v >= 0 || 'Price must be positive']"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-select
                                v-model="formData.gst_rate"
                                :items="gstRates"
                                label="GST Rate"
                                suffix="%"
                                :rules="[v => v !== null || 'GST rate is required']"
                            ></v-select>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="6">
                            <v-text-field
                                v-model.number="formData.stock_quantity"
                                label="Stock Quantity"
                                type="number"
                                min="0"
                                hint="Leave empty for service items"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field
                                v-model.number="formData.min_stock_level"
                                label="Min Stock Level"
                                type="number"
                                min="0"
                                hint="Alert when stock falls below"
                            ></v-text-field>
                        </v-col>
                    </v-row>
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
import { useProductStore } from '../stores/product';

const props = defineProps({
    modelValue: Boolean,
    product: Object,
});

const emit = defineEmits(['update:modelValue', 'saved']);

const productStore = useProductStore();

const form = ref(null);
const valid = ref(false);
const loading = ref(false);

const dialog = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const isEdit = computed(() => !!props.product);

const formData = ref({
    name: '',
    description: '',
    hsn_code: '',
    unit: 'Nos',
    price: 0,
    gst_rate: 18,
    stock_quantity: null,
    min_stock_level: null,
});

const units = [
    'Nos',
    'Pcs',
    'Kg',
    'Gm',
    'Ltr',
    'Ml',
    'Mtr',
    'Cm',
    'Sqft',
    'Sqm',
    'Box',
    'Pair',
    'Set',
    'Dozen',
    'Hrs',
    'Days',
];

const gstRates = [
    { title: '0%', value: 0 },
    { title: '5%', value: 5 },
    { title: '12%', value: 12 },
    { title: '18%', value: 18 },
    { title: '28%', value: 28 },
];

const hsnRules = [
    (v) => !!v || 'HSN code is required',
    (v) => /^\d{4,8}$/.test(v) || 'HSN code must be 4-8 digits',
];

watch(() => props.modelValue, (newVal) => {
    if (newVal) {
        if (props.product) {
            formData.value = { ...props.product };
        } else {
            resetForm();
        }
    }
});

const resetForm = () => {
    formData.value = {
        name: '',
        description: '',
        hsn_code: '',
        unit: 'Nos',
        price: 0,
        gst_rate: 18,
        stock_quantity: null,
        min_stock_level: null,
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
        let savedProduct;
        if (isEdit.value) {
            savedProduct = await productStore.updateProduct(props.product.id, formData.value);
        } else {
            savedProduct = await productStore.createProduct(formData.value);
        }
        emit('saved', savedProduct);
        close();
    } catch (error) {
        console.error('Failed to save product:', error);
    } finally {
        loading.value = false;
    }
};
</script>
