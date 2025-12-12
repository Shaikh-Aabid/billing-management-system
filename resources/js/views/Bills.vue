<template>
    <div>
        <!-- Header -->
        <div class="d-flex justify-space-between align-center mb-6">
            <div>
                <h1 class="text-h4 font-weight-bold">Bills</h1>
                <p class="text-body-2 text-medium-emphasis mt-1">Manage your invoices and billing records</p>
            </div>
            <v-btn color="primary" prepend-icon="mdi-plus" to="/bills/create" size="large">
                Create Bill
            </v-btn>
        </div>

        <!-- Stats Summary -->
        <v-row class="mb-4">
            <v-col cols="12" sm="4">
                <v-card>
                    <v-card-text class="d-flex align-center pa-4">
                        <v-avatar color="primary" variant="tonal" size="48" rounded="lg" class="mr-4">
                            <v-icon>mdi-receipt-text-outline</v-icon>
                        </v-avatar>
                        <div>
                            <p class="text-caption text-medium-emphasis mb-0">Total Bills</p>
                            <p class="text-h5 font-weight-bold mb-0">{{ billStore.pagination.total }}</p>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="4">
                <v-card>
                    <v-card-text class="d-flex align-center pa-4">
                        <v-avatar color="success" variant="tonal" size="48" rounded="lg" class="mr-4">
                            <v-icon>mdi-currency-inr</v-icon>
                        </v-avatar>
                        <div>
                            <p class="text-caption text-medium-emphasis mb-0">Total Revenue</p>
                            <p class="text-h5 font-weight-bold mb-0">{{ formatCurrency(totalRevenue) }}</p>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="4">
                <v-card>
                    <v-card-text class="d-flex align-center pa-4">
                        <v-avatar color="warning" variant="tonal" size="48" rounded="lg" class="mr-4">
                            <v-icon>mdi-percent-outline</v-icon>
                        </v-avatar>
                        <div>
                            <p class="text-caption text-medium-emphasis mb-0">GST Collected</p>
                            <p class="text-h5 font-weight-bold mb-0">{{ formatCurrency(totalGst) }}</p>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Filters -->
        <v-card class="mb-4">
            <v-card-text class="pa-4">
                <v-row align="center">
                    <v-col cols="12" md="4">
                        <v-text-field
                            v-model="filters.search"
                            placeholder="Search bills..."
                            prepend-inner-icon="mdi-magnify"
                            clearable
                            hide-details
                            density="comfortable"
                            variant="outlined"
                            @input="debouncedSearch"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-text-field
                            v-model="filters.date_from"
                            label="From Date"
                            type="date"
                            hide-details
                            density="comfortable"
                            @change="loadBills(1)"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-text-field
                            v-model="filters.date_to"
                            label="To Date"
                            type="date"
                            hide-details
                            density="comfortable"
                            @change="loadBills(1)"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="2">
                        <v-btn
                            color="secondary"
                            variant="tonal"
                            block
                            @click="clearFilters"
                            prepend-icon="mdi-filter-remove"
                        >
                            Clear
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Bills Table -->
        <v-card>
            <v-data-table-server
                :headers="headers"
                :items="billStore.bills"
                :items-length="billStore.pagination.total"
                :loading="billStore.loading"
                :page="billStore.pagination.currentPage"
                :items-per-page="billStore.pagination.perPage"
                @update:page="onPageChange"
                @update:items-per-page="onItemsPerPageChange"
                class="bills-table"
            >
                <template v-slot:item.bill_number="{ item }">
                    <div class="d-flex align-center py-2">
                        <v-avatar :color="item.status === 'cancelled' ? 'error' : 'primary'" variant="tonal" size="36" rounded="lg" class="mr-3">
                            <v-icon size="18">{{ item.status === 'cancelled' ? 'mdi-cancel' : 'mdi-receipt-text' }}</v-icon>
                        </v-avatar>
                        <div>
                            <router-link :to="`/bills/${item.id}`" :class="item.status === 'cancelled' ? 'text-error' : 'text-primary'" class="font-weight-bold text-decoration-none">
                                {{ item.cancelled_bill_number || item.bill_number }}
                            </router-link>
                            <div class="d-flex align-center ga-2">
                                <p class="text-caption text-medium-emphasis mb-0">{{ formatDate(item.bill_date) }}</p>
                                <v-chip v-if="item.status === 'cancelled'" size="x-small" color="error" variant="flat">
                                    Cancelled
                                </v-chip>
                            </div>
                        </div>
                    </div>
                </template>

                <template v-slot:item.party="{ item }">
                    <div class="d-flex align-center">
                        <v-avatar color="secondary" variant="tonal" size="32" class="mr-2">
                            <span class="text-caption font-weight-medium">{{ getInitials(item.party?.name) }}</span>
                        </v-avatar>
                        <span>{{ item.party?.name || 'N/A' }}</span>
                    </div>
                </template>

                <template v-slot:item.subtotal="{ item }">
                    <span class="font-weight-medium">{{ formatCurrency(item.subtotal) }}</span>
                </template>

                <template v-slot:item.gst_amount="{ item }">
                    <v-chip size="small" color="warning" variant="tonal" class="font-weight-medium">
                        {{ formatCurrency(item.gst_amount) }}
                    </v-chip>
                </template>

                <template v-slot:item.total_amount="{ item }">
                    <span class="text-success font-weight-bold text-body-1">
                        {{ formatCurrency(item.total_amount) }}
                    </span>
                </template>

                <template v-slot:item.has_eway_bill="{ item }">
                    <v-chip
                        :color="item.ewayBill ? 'success' : 'grey'"
                        size="small"
                        variant="tonal"
                        :prepend-icon="item.ewayBill ? 'mdi-check-circle' : 'mdi-close-circle'"
                    >
                        {{ item.ewayBill ? 'Generated' : 'None' }}
                    </v-chip>
                </template>

                <template v-slot:item.actions="{ item }">
                    <div class="d-flex justify-center ga-1">
                        <v-tooltip text="View Details" location="top">
                            <template v-slot:activator="{ props }">
                                <v-btn
                                    v-bind="props"
                                    icon="mdi-eye-outline"
                                    variant="text"
                                    size="small"
                                    color="primary"
                                    :to="`/bills/${item.id}`"
                                ></v-btn>
                            </template>
                        </v-tooltip>
                        <v-tooltip text="Download PDF" location="top">
                            <template v-slot:activator="{ props }">
                                <v-btn
                                    v-bind="props"
                                    icon="mdi-file-pdf-box"
                                    variant="text"
                                    size="small"
                                    color="error"
                                    @click="downloadPdf(item.id)"
                                ></v-btn>
                            </template>
                        </v-tooltip>
                        <v-tooltip text="Delete" location="top">
                            <template v-slot:activator="{ props }">
                                <v-btn
                                    v-bind="props"
                                    icon="mdi-delete-outline"
                                    variant="text"
                                    size="small"
                                    color="error"
                                    @click="confirmDelete(item)"
                                ></v-btn>
                            </template>
                        </v-tooltip>
                    </div>
                </template>

                <template v-slot:loading>
                    <v-skeleton-loader type="table-row@5"></v-skeleton-loader>
                </template>

                <template v-slot:no-data>
                    <div class="text-center py-8">
                        <v-icon size="64" color="grey-lighten-1" class="mb-4">mdi-receipt-text-outline</v-icon>
                        <h3 class="text-h6 mb-2">No bills found</h3>
                        <p class="text-body-2 text-medium-emphasis mb-4">Get started by creating your first invoice</p>
                        <v-btn color="primary" to="/bills/create" prepend-icon="mdi-plus">
                            Create Bill
                        </v-btn>
                    </div>
                </template>
            </v-data-table-server>
        </v-card>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="showDeleteDialog" max-width="400">
            <v-card>
                <v-card-title class="d-flex align-center pa-5">
                    <v-avatar color="error" variant="tonal" size="40" class="mr-3">
                        <v-icon>mdi-alert-outline</v-icon>
                    </v-avatar>
                    <span>Confirm Delete</span>
                </v-card-title>
                <v-card-text class="pa-5 pt-0">
                    Are you sure you want to delete bill <strong>{{ billToDelete?.cancelled_bill_number || billToDelete?.bill_number }}</strong>?
                    This action cannot be undone.
                </v-card-text>
                <v-card-actions class="pa-5 pt-0">
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="showDeleteDialog = false">Cancel</v-btn>
                    <v-btn
                        color="error"
                        variant="flat"
                        :loading="billStore.loading"
                        @click="deleteBill"
                    >
                        Delete
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue';
import { useBillStore } from '../stores/bill';

const billStore = useBillStore();
const showSnackbar = inject('showSnackbar');

const showDeleteDialog = ref(false);
const billToDelete = ref(null);

const filters = ref({
    search: '',
    date_from: '',
    date_to: '',
});

const headers = [
    { title: 'Invoice', key: 'bill_number', sortable: true },
    { title: 'Party', key: 'party', sortable: false },
    { title: 'Subtotal', key: 'subtotal', sortable: true },
    { title: 'GST', key: 'gst_amount', sortable: true },
    { title: 'Total', key: 'total_amount', sortable: true },
    { title: 'E-Way Bill', key: 'has_eway_bill', sortable: false },
    { title: 'Actions', key: 'actions', sortable: false, align: 'center', width: 140 },
];

const totalRevenue = computed(() => {
    if (!billStore.bills || billStore.bills.length === 0) return 0;
    return billStore.bills.reduce((sum, bill) => sum + (parseFloat(bill.total_amount) || 0), 0);
});

const totalGst = computed(() => {
    if (!billStore.bills || billStore.bills.length === 0) return 0;
    return billStore.bills.reduce((sum, bill) => sum + (parseFloat(bill.gst_amount) || 0), 0);
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount || 0);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const getInitials = (name) => {
    if (!name) return 'N/A';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

let searchTimeout = null;
const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadBills(1);
    }, 300);
};

const loadBills = async (page = 1) => {
    try {
        await billStore.fetchBills(page, filters.value);
    } catch (error) {
        showSnackbar('Failed to load bills', 'error');
    }
};

const onPageChange = (page) => {
    loadBills(page);
};

const onItemsPerPageChange = () => {
    loadBills(1);
};

const clearFilters = () => {
    filters.value = {
        search: '',
        date_from: '',
        date_to: '',
    };
    loadBills(1);
};

const downloadPdf = async (id) => {
    try {
        await billStore.downloadBillPdf(id);
        showSnackbar('PDF downloaded successfully!', 'success');
    } catch (error) {
        showSnackbar('Failed to download PDF', 'error');
    }
};

const confirmDelete = (bill) => {
    billToDelete.value = bill;
    showDeleteDialog.value = true;
};

const deleteBill = async () => {
    try {
        await billStore.deleteBill(billToDelete.value.id);
        showDeleteDialog.value = false;
        showSnackbar('Bill deleted successfully!', 'success');
    } catch (error) {
        showSnackbar('Failed to delete bill', 'error');
    }
};

onMounted(() => {
    loadBills();
});
</script>

<style scoped>
.bills-table :deep(th) {
    font-weight: 600 !important;
    text-transform: uppercase;
    font-size: 0.75rem !important;
    letter-spacing: 0.5px;
}

.bills-table :deep(tr:hover) {
    background: rgba(99, 102, 241, 0.04) !important;
}

.ga-1 {
    gap: 4px;
}

.ga-2 {
    gap: 8px;
}
</style>
