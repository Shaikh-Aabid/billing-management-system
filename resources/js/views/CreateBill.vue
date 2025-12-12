<template>
    <div>
        <div class="d-flex justify-space-between align-center mb-6">
            <h1 class="text-h4">Create New Bill</h1>
            <v-btn variant="text" prepend-icon="mdi-arrow-left" to="/bills">
                Back to Bills
            </v-btn>
        </div>

        <v-form ref="form" v-model="valid">
            <v-row>
                <!-- Bill Details -->
                <v-col cols="12" md="8">
                    <v-card class="mb-4">
                        <v-card-title>Bill Details</v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-autocomplete
                                        v-model="billData.party_id"
                                        :items="parties"
                                        item-title="name"
                                        item-value="id"
                                        label="Select Party"
                                        :rules="[v => !!v || 'Party is required']"
                                        :loading="loadingParties"
                                        @update:model-value="onPartyChange"
                                    >
                                        <template v-slot:append>
                                            <v-btn
                                                icon="mdi-plus"
                                                variant="text"
                                                size="small"
                                                @click="showPartyDialog = true"
                                            ></v-btn>
                                        </template>
                                    </v-autocomplete>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="billData.bill_date"
                                        label="Bill Date"
                                        type="date"
                                        :rules="[v => !!v || 'Bill date is required']"
                                    ></v-text-field>
                                </v-col>
                            </v-row>

                            <!-- Party Details Display -->
                            <v-alert
                                v-if="selectedParty"
                                type="info"
                                variant="tonal"
                                class="mt-2"
                            >
                                <div><strong>Address:</strong> {{ selectedParty.address }}</div>
                                <div v-if="selectedParty.gstin">
                                    <strong>GSTIN:</strong> {{ selectedParty.gstin }}
                                </div>
                                <div v-if="selectedParty.state">
                                    <strong>State:</strong> {{ selectedParty.state }}
                                </div>
                            </v-alert>

                            <!-- Inter-State Transaction Option -->
                            <v-checkbox
                                v-model="billData.is_out_of_maharashtra"
                                label="Out of Maharashtra (Inter-State)"
                                hint="Check this if the party is outside Maharashtra. IGST will be applied instead of CGST + SGST."
                                persistent-hint
                                color="primary"
                                class="mt-4"
                            ></v-checkbox>
                        </v-card-text>
                    </v-card>

                    <!-- Bill Items -->
                    <v-card class="mb-4">
                        <v-card-title class="d-flex justify-space-between align-center">
                            Bill Items
                            <v-btn
                                color="primary"
                                size="small"
                                prepend-icon="mdi-plus"
                                @click="addItem"
                            >
                                Add Item
                            </v-btn>
                        </v-card-title>
                        <v-card-text>
                            <v-table density="comfortable">
                                <thead>
                                    <tr>
                                        <th style="width: 30%">Product</th>
                                        <th style="width: 10%">HSN</th>
                                        <th style="width: 10%">Qty</th>
                                        <th style="width: 15%">Price</th>
                                        <th style="width: 10%">GST %</th>
                                        <th style="width: 15%">Amount</th>
                                        <th style="width: 10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in billData.items" :key="index">
                                        <td>
                                            <v-autocomplete
                                                v-model="item.product_id"
                                                :items="products"
                                                item-title="name"
                                                item-value="id"
                                                density="compact"
                                                hide-details
                                                @update:model-value="onProductChange(index)"
                                            ></v-autocomplete>
                                        </td>
                                        <td>
                                            <v-text-field
                                                v-model="item.hsn_code"
                                                density="compact"
                                                hide-details
                                                readonly
                                            ></v-text-field>
                                        </td>
                                        <td>
                                            <v-text-field
                                                v-model.number="item.quantity"
                                                type="number"
                                                min="1"
                                                density="compact"
                                                hide-details
                                                @input="calculateItemAmount(index)"
                                            ></v-text-field>
                                        </td>
                                        <td>
                                            <v-text-field
                                                v-model.number="item.price"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                density="compact"
                                                hide-details
                                                @input="calculateItemAmount(index)"
                                            ></v-text-field>
                                        </td>
                                        <td>
                                            <v-text-field
                                                v-model.number="item.gst_rate"
                                                type="number"
                                                min="0"
                                                max="28"
                                                density="compact"
                                                hide-details
                                                @input="calculateItemAmount(index)"
                                            ></v-text-field>
                                        </td>
                                        <td class="text-right font-weight-bold">
                                            {{ formatCurrency(item.amount) }}
                                        </td>
                                        <td class="text-center">
                                            <v-btn
                                                icon="mdi-delete"
                                                variant="text"
                                                size="small"
                                                color="error"
                                                @click="removeItem(index)"
                                                :disabled="billData.items.length === 1"
                                            ></v-btn>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>

                            <v-alert
                                v-if="billData.items.length === 0"
                                type="warning"
                                variant="tonal"
                                class="mt-4"
                            >
                                Please add at least one item to the bill.
                            </v-alert>
                        </v-card-text>
                    </v-card>

                    <!-- Notes -->
                    <v-card>
                        <v-card-title>Additional Information</v-card-title>
                        <v-card-text>
                            <v-textarea
                                v-model="billData.notes"
                                label="Notes"
                                rows="3"
                                hint="Any additional notes for this bill"
                            ></v-textarea>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Bill Summary -->
                <v-col cols="12" md="4">
                    <v-card class="sticky-card">
                        <v-card-title>Bill Summary</v-card-title>
                        <v-card-text>
                            <v-list>
                                <v-list-item>
                                    <template v-slot:prepend>
                                        <span>Subtotal:</span>
                                    </template>
                                    <template v-slot:append>
                                        <span class="font-weight-bold">
                                            {{ formatCurrency(summary.subtotal) }}
                                        </span>
                                    </template>
                                </v-list-item>

                                <v-divider v-if="isInterState"></v-divider>

                                <template v-if="isInterState">
                                    <v-list-item>
                                        <template v-slot:prepend>
                                            <span>IGST:</span>
                                        </template>
                                        <template v-slot:append>
                                            <span>{{ formatCurrency(summary.igst) }}</span>
                                        </template>
                                    </v-list-item>
                                </template>

                                <template v-else>
                                    <v-list-item>
                                        <template v-slot:prepend>
                                            <span>CGST:</span>
                                        </template>
                                        <template v-slot:append>
                                            <span>{{ formatCurrency(summary.cgst) }}</span>
                                        </template>
                                    </v-list-item>
                                    <v-list-item>
                                        <template v-slot:prepend>
                                            <span>SGST:</span>
                                        </template>
                                        <template v-slot:append>
                                            <span>{{ formatCurrency(summary.sgst) }}</span>
                                        </template>
                                    </v-list-item>
                                </template>

                                <v-divider></v-divider>

                                <v-list-item>
                                    <template v-slot:prepend>
                                        <span>Total GST:</span>
                                    </template>
                                    <template v-slot:append>
                                        <span class="text-warning font-weight-bold">
                                            {{ formatCurrency(summary.totalGst) }}
                                        </span>
                                    </template>
                                </v-list-item>

                                <v-divider></v-divider>

                                <v-list-item class="bg-primary rounded mt-2">
                                    <template v-slot:prepend>
                                        <span class="text-h6 text-white">Grand Total:</span>
                                    </template>
                                    <template v-slot:append>
                                        <span class="text-h5 text-white font-weight-bold">
                                            {{ formatCurrency(summary.grandTotal) }}
                                        </span>
                                    </template>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                        <v-card-actions class="flex-column">
                            <v-btn
                                color="primary"
                                block
                                size="large"
                                :loading="saving"
                                :disabled="!valid || billData.items.length === 0"
                                @click="saveBill"
                            >
                                Create Bill
                            </v-btn>
                            <v-btn
                                color="secondary"
                                variant="outlined"
                                block
                                class="mt-2"
                                @click="resetForm"
                            >
                                Reset
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </v-form>

        <!-- Party Dialog -->
        <PartyDialog
            v-model="showPartyDialog"
            @saved="onNewPartySaved"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, inject, watch } from 'vue';
import { useRouter } from 'vue-router';
import { usePartyStore } from '../stores/party';
import { useProductStore } from '../stores/product';
import { useBillStore } from '../stores/bill';
import PartyDialog from '../components/PartyDialog.vue';

const router = useRouter();
const partyStore = usePartyStore();
const productStore = useProductStore();
const billStore = useBillStore();
const showSnackbar = inject('showSnackbar');

const form = ref(null);
const valid = ref(false);
const saving = ref(false);
const loadingParties = ref(false);
const showPartyDialog = ref(false);

const parties = ref([]);
const products = ref([]);
const selectedParty = ref(null);

const billData = ref({
    party_id: null,
    bill_date: new Date().toISOString().split('T')[0],
    notes: '',
    is_out_of_maharashtra: false,
    items: [createEmptyItem()],
});

function createEmptyItem() {
    return {
        product_id: null,
        hsn_code: '',
        quantity: 1,
        price: 0,
        gst_rate: 18,
        amount: 0,
    };
}

const isInterState = computed(() => {
    // Use the checkbox value to determine if it's an inter-state transaction
    // If checked (out of Maharashtra), IGST will be applied instead of CGST + SGST
    return billData.value.is_out_of_maharashtra;
});

const summary = computed(() => {
    let subtotal = 0;
    let totalGst = 0;

    billData.value.items.forEach(item => {
        const itemSubtotal = item.quantity * item.price;
        const itemGst = itemSubtotal * (item.gst_rate / 100);
        subtotal += itemSubtotal;
        totalGst += itemGst;
    });

    const cgst = isInterState.value ? 0 : totalGst / 2;
    const sgst = isInterState.value ? 0 : totalGst / 2;
    const igst = isInterState.value ? totalGst : 0;

    return {
        subtotal,
        cgst,
        sgst,
        igst,
        totalGst,
        grandTotal: subtotal + totalGst,
    };
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
    }).format(amount || 0);
};

const loadParties = async () => {
    loadingParties.value = true;
    try {
        parties.value = await partyStore.fetchAllParties();
    } catch (error) {
        showSnackbar('Failed to load parties', 'error');
    } finally {
        loadingParties.value = false;
    }
};

const loadProducts = async () => {
    try {
        products.value = await productStore.fetchAllProducts();
    } catch (error) {
        showSnackbar('Failed to load products', 'error');
    }
};

const onPartyChange = () => {
    selectedParty.value = parties.value.find(p => p.id === billData.value.party_id);
};

const onProductChange = (index) => {
    const product = products.value.find(p => p.id === billData.value.items[index].product_id);
    if (product) {
        billData.value.items[index].hsn_code = product.hsn_code;
        billData.value.items[index].price = product.price;
        billData.value.items[index].gst_rate = product.gst_rate;
        calculateItemAmount(index);
    }
};

const calculateItemAmount = (index) => {
    const item = billData.value.items[index];
    const subtotal = item.quantity * item.price;
    const gst = subtotal * (item.gst_rate / 100);
    item.amount = subtotal + gst;
};

const addItem = () => {
    billData.value.items.push(createEmptyItem());
};

const removeItem = (index) => {
    if (billData.value.items.length > 1) {
        billData.value.items.splice(index, 1);
    }
};

const resetForm = () => {
    billData.value = {
        party_id: null,
        bill_date: new Date().toISOString().split('T')[0],
        notes: '',
        is_out_of_maharashtra: false,
        items: [createEmptyItem()],
    };
    selectedParty.value = null;
};

const onNewPartySaved = async (party) => {
    await loadParties();
    billData.value.party_id = party.id;
    onPartyChange();
};

const saveBill = async () => {
    if (!valid.value || billData.value.items.length === 0) return;

    saving.value = true;
    try {
        const payload = {
            ...billData.value,
            subtotal: summary.value.subtotal,
            cgst: summary.value.cgst,
            sgst: summary.value.sgst,
            igst: summary.value.igst,
            gst_amount: summary.value.totalGst,
            total_amount: summary.value.grandTotal,
            is_inter_state: isInterState.value,
        };

        const bill = await billStore.createBill(payload);
        showSnackbar('Bill created successfully!', 'success');
        router.push(`/bills/${bill.id}`);
    } catch (error) {
        showSnackbar(error.response?.data?.message || 'Failed to create bill', 'error');
    } finally {
        saving.value = false;
    }
};

onMounted(() => {
    loadParties();
    loadProducts();
});
</script>

<style scoped>
.sticky-card {
    position: sticky;
    top: 80px;
}
</style>
