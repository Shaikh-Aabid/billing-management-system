<template>
    <div>
        <div class="d-flex justify-space-between align-center mb-6">
            <div>
                <h1 class="text-h4">Bill #{{ bill?.cancelled_bill_number || bill?.bill_number }}</h1>
                <p class="text-subtitle-1 text-grey">
                    Created on {{ formatDate(bill?.created_at) }}
                </p>
            </div>
            <div class="d-flex align-center">
                <v-chip
                    v-if="bill?.status === 'cancelled'"
                    color="error"
                    class="mr-4"
                    size="large"
                >
                    CANCELLED
                </v-chip>
                <v-btn
                    color="error"
                    prepend-icon="mdi-file-pdf-box"
                    class="mr-2"
                    @click="downloadPdf"
                    :loading="downloading"
                >
                    Download PDF
                </v-btn>
                <v-btn
                    v-if="!bill?.ewayBill"
                    color="info"
                    prepend-icon="mdi-truck"
                    class="mr-2"
                    @click="showEwayDialog = true"
                >
                    Generate E-Way Bill
                </v-btn>
                <v-btn
                    v-if="bill?.status !== 'cancelled' && !bill?.ewayBill"
                    color="warning"
                    variant="outlined"
                    prepend-icon="mdi-cancel"
                    class="mr-2"
                    @click="showCancelDialog = true"
                >
                    Cancel Bill
                </v-btn>
                <v-btn
                    color="primary"
                    prepend-icon="mdi-pencil"
                    class="mr-2"
                    :to="`/bills/edit/${bill?.id}`"
                >
                    Edit
                </v-btn>
                <v-btn variant="text" prepend-icon="mdi-arrow-left" to="/bills">
                    Back
                </v-btn>
            </div>
        </div>

        <v-row v-if="bill">
            <!-- Bill Info -->
            <v-col cols="12" md="8">
                <v-card class="mb-4">
                    <v-card-title>Party Details</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" md="6">
                                <p class="text-subtitle-2 text-grey">Party Name</p>
                                <p class="text-body-1 font-weight-bold">{{ bill.party?.name }}</p>
                            </v-col>
                            <v-col cols="12" md="6">
                                <p class="text-subtitle-2 text-grey">GSTIN</p>
                                <p class="text-body-1">{{ bill.party?.gstin || 'N/A' }}</p>
                            </v-col>
                            <v-col cols="12">
                                <p class="text-subtitle-2 text-grey">Address</p>
                                <p class="text-body-1">{{ bill.party?.address }}</p>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>

                <!-- Items Table -->
                <v-card class="mb-4">
                    <v-card-title>Bill Items</v-card-title>
                    <v-table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>HSN Code</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Price</th>
                                <th class="text-right">GST %</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in bill.items" :key="item.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ item.product?.name }}</td>
                                <td>
                                    <v-chip size="small" color="info" variant="tonal">
                                        {{ item.hsn_code }}
                                    </v-chip>
                                </td>
                                <td class="text-right">{{ item.quantity }} {{ item.product?.unit }}</td>
                                <td class="text-right">{{ formatCurrency(item.price) }}</td>
                                <td class="text-right">{{ item.gst_rate }}%</td>
                                <td class="text-right font-weight-bold">
                                    {{ formatCurrency(item.amount) }}
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                </v-card>

                <!-- E-Way Bill Info -->
                <v-card v-if="bill.ewayBill" class="mb-4">
                    <v-card-title class="d-flex justify-space-between align-center">
                        E-Way Bill Details
                        <v-chip color="success" size="small">Active</v-chip>
                    </v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" md="4">
                                <p class="text-subtitle-2 text-grey">E-Way Bill Number</p>
                                <p class="text-body-1 font-weight-bold">
                                    {{ bill.ewayBill.eway_bill_number }}
                                </p>
                            </v-col>
                            <v-col cols="12" md="4">
                                <p class="text-subtitle-2 text-grey">Vehicle Number</p>
                                <p class="text-body-1">{{ bill.ewayBill.vehicle_number }}</p>
                            </v-col>
                            <v-col cols="12" md="4">
                                <p class="text-subtitle-2 text-grey">Valid Until</p>
                                <p class="text-body-1">
                                    {{ formatDate(bill.ewayBill.valid_until) }}
                                </p>
                            </v-col>
                            <v-col cols="12" md="4">
                                <p class="text-subtitle-2 text-grey">Transport Mode</p>
                                <p class="text-body-1">{{ bill.ewayBill.transport_mode }}</p>
                            </v-col>
                            <v-col cols="12" md="4">
                                <p class="text-subtitle-2 text-grey">Distance (km)</p>
                                <p class="text-body-1">{{ bill.ewayBill.distance }}</p>
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn
                            color="error"
                            prepend-icon="mdi-file-pdf-box"
                            @click="downloadEwayPdf"
                        >
                            Download E-Way Bill PDF
                        </v-btn>
                    </v-card-actions>
                </v-card>

                <!-- Notes -->
                <v-card v-if="bill.notes">
                    <v-card-title>Notes</v-card-title>
                    <v-card-text>{{ bill.notes }}</v-card-text>
                </v-card>
            </v-col>

            <!-- Summary -->
            <v-col cols="12" md="4">
                <v-card class="sticky-card">
                    <v-card-title>Bill Summary</v-card-title>
                    <v-card-text>
                        <v-list>
                            <v-list-item>
                                <template v-slot:prepend>
                                    <span>Bill Date:</span>
                                </template>
                                <template v-slot:append>
                                    <span class="font-weight-bold">
                                        {{ formatDate(bill.bill_date) }}
                                    </span>
                                </template>
                            </v-list-item>

                            <template v-if="bill.gst_amount > 0">
                                <v-divider></v-divider>
                                <template v-if="bill.is_inter_state">
                                    <v-list-item>
                                        <template v-slot:prepend>
                                            <span>IGST:</span>
                                        </template>
                                        <template v-slot:append>
                                            <span>{{ formatCurrency(bill.igst) }}</span>
                                        </template>
                                    </v-list-item>
                                </template>
                                <template v-else>
                                    <v-list-item>
                                        <template v-slot:prepend>
                                            <span>CGST:</span>
                                        </template>
                                        <template v-slot:append>
                                            <span>{{ formatCurrency(bill.cgst) }}</span>
                                        </template>
                                    </v-list-item>
                                    <v-list-item>
                                        <template v-slot:prepend>
                                            <span>SGST:</span>
                                        </template>
                                        <template v-slot:append>
                                            <span>{{ formatCurrency(bill.sgst) }}</span>
                                        </template>
                                    </v-list-item>
                                </template>
                            </template>

                            <template v-if="bill.gst_amount > 0">
                                <v-divider></v-divider>

                                <v-list-item>
                                    <template v-slot:prepend>
                                        <span>Total GST:</span>
                                    </template>
                                    <template v-slot:append>
                                        <span class="text-warning font-weight-bold">
                                            {{ formatCurrency(bill.gst_amount) }}
                                        </span>
                                    </template>
                                </v-list-item>
                            </template>

                            <v-divider></v-divider>

                            <v-list-item class="bg-primary rounded mt-2">
                                <template v-slot:prepend>
                                    <span class="text-h6 text-white">Grand Total:</span>
                                </template>
                                <template v-slot:append>
                                    <span class="text-h5 text-white font-weight-bold">
                                        {{ formatCurrency(bill.total_amount) }}
                                    </span>
                                </template>
                            </v-list-item>
                        </v-list>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-progress-circular
            v-else
            indeterminate
            color="primary"
            class="d-block mx-auto mt-10"
        ></v-progress-circular>

        <!-- E-Way Bill Dialog -->
        <EwayBillDialog
            v-model="showEwayDialog"
            :bill="bill"
            @generated="onEwayGenerated"
        />

        <!-- Cancel Bill Confirmation Dialog -->
        <v-dialog v-model="showCancelDialog" max-width="500">
            <v-card>
                <v-card-title class="text-h5 text-warning">
                    Cancel Bill
                </v-card-title>
                <v-card-text>
                    <v-alert type="warning" variant="tonal" class="mb-4">
                        Are you sure you want to cancel this bill?
                    </v-alert>
                    <p class="text-body-1">
                        <strong>Bill Number:</strong> {{ bill?.bill_number }}<br>
                        <strong>Party:</strong> {{ bill?.party?.name }}<br>
                        <strong>Amount:</strong> {{ formatCurrency(bill?.total_amount) }}
                    </p>
                    <p class="text-body-2 text-grey mt-4">
                        This action will mark the bill as cancelled. The invoice number will be available for reuse by the next bill.
                    </p>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        variant="text"
                        @click="showCancelDialog = false"
                    >
                        No, Keep Bill
                    </v-btn>
                    <v-btn
                        color="warning"
                        variant="elevated"
                        :loading="cancelling"
                        @click="cancelBill"
                    >
                        Yes, Cancel Bill
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue';
import { useRoute } from 'vue-router';
import { useBillStore } from '../stores/bill';
import EwayBillDialog from '../components/EwayBillDialog.vue';

const route = useRoute();
const billStore = useBillStore();
const showSnackbar = inject('showSnackbar');

const bill = ref(null);
const downloading = ref(false);
const showEwayDialog = ref(false);
const showCancelDialog = ref(false);
const cancelling = ref(false);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
    }).format(amount || 0);
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const loadBill = async () => {
    try {
        bill.value = await billStore.fetchBill(route.params.id);
    } catch (error) {
        showSnackbar('Failed to load bill', 'error');
    }
};

const downloadPdf = async () => {
    downloading.value = true;
    try {
        await billStore.downloadBillPdf(bill.value.id);
        showSnackbar('PDF downloaded successfully!', 'success');
    } catch (error) {
        showSnackbar('Failed to download PDF', 'error');
    } finally {
        downloading.value = false;
    }
};

const downloadEwayPdf = async () => {
    try {
        await billStore.downloadEwayBillPdf(bill.value.eway_bill.id);
        showSnackbar('E-Way Bill PDF downloaded!', 'success');
    } catch (error) {
        showSnackbar('Failed to download E-Way Bill PDF', 'error');
    }
};

const onEwayGenerated = () => {
    showSnackbar('E-Way Bill generated successfully!', 'success');
    loadBill();
};

const cancelBill = async () => {
    cancelling.value = true;
    try {
        await billStore.updateBill(bill.value.id, { status: 'cancelled' });
        showSnackbar('Bill cancelled successfully!', 'success');
        showCancelDialog.value = false;
        loadBill();
    } catch (error) {
        showSnackbar(error.response?.data?.message || 'Failed to cancel bill', 'error');
    } finally {
        cancelling.value = false;
    }
};

onMounted(() => {
    loadBill();
});
</script>

<style scoped>
.sticky-card {
    position: sticky;
    top: 80px;
}
</style>
