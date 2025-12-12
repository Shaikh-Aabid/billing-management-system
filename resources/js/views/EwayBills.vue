<template>
    <div>
        <div class="d-flex justify-space-between align-center mb-6">
            <h1 class="text-h4">E-Way Bills</h1>
        </div>

        <!-- Filters -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="4">
                        <v-text-field
                            v-model="filters.search"
                            label="Search by E-Way Bill No..."
                            prepend-inner-icon="mdi-magnify"
                            clearable
                            hide-details
                            @input="debouncedSearch"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-select
                            v-model="filters.status"
                            :items="statusOptions"
                            label="Status"
                            clearable
                            hide-details
                            @update:model-value="loadEwayBills(1)"
                        ></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-text-field
                            v-model="filters.date"
                            label="Date"
                            type="date"
                            hide-details
                            @change="loadEwayBills(1)"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="2">
                        <v-btn
                            color="secondary"
                            variant="outlined"
                            block
                            @click="clearFilters"
                        >
                            Clear
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- E-Way Bills Table -->
        <v-card>
            <v-data-table-server
                :headers="headers"
                :items="billStore.ewayBills"
                :items-length="billStore.pagination.total"
                :loading="billStore.loading"
                :page="billStore.pagination.currentPage"
                :items-per-page="billStore.pagination.perPage"
                @update:page="onPageChange"
                @update:items-per-page="onItemsPerPageChange"
            >
                <template v-slot:item.eway_bill_number="{ item }">
                    <span class="font-weight-bold text-primary">
                        {{ item.eway_bill_number }}
                    </span>
                </template>

                <template v-slot:item.bill="{ item }">
                    <router-link :to="`/bills/${item.bill_id}`" class="text-primary">
                        {{ item.bill?.bill_number }}
                    </router-link>
                </template>

                <template v-slot:item.party="{ item }">
                    {{ item.bill?.party?.name || 'N/A' }}
                </template>

                <template v-slot:item.generated_at="{ item }">
                    {{ formatDate(item.generated_at) }}
                </template>

                <template v-slot:item.valid_until="{ item }">
                    <v-chip
                        :color="isExpired(item.valid_until) ? 'error' : 'success'"
                        size="small"
                        variant="tonal"
                    >
                        {{ formatDate(item.valid_until) }}
                    </v-chip>
                </template>

                <template v-slot:item.transport_mode="{ item }">
                    <v-chip size="small" color="info" variant="tonal">
                        {{ item.transport_mode }}
                    </v-chip>
                </template>

                <template v-slot:item.actions="{ item }">
                    <div class="d-flex justify-center ga-1">
                        <v-tooltip text="View Bill" location="top">
                            <template v-slot:activator="{ props }">
                                <v-btn
                                    v-bind="props"
                                    icon="mdi-eye"
                                    variant="text"
                                    size="small"
                                    color="primary"
                                    :to="`/bills/${item.bill_id}`"
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
                    </div>
                </template>
            </v-data-table-server>
        </v-card>
    </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue';
import { useBillStore } from '../stores/bill';

const billStore = useBillStore();
const showSnackbar = inject('showSnackbar');

const filters = ref({
    search: '',
    status: null,
    date: '',
});

const statusOptions = [
    { title: 'Active', value: 'active' },
    { title: 'Expired', value: 'expired' },
];

const headers = [
    { title: 'E-Way Bill No.', key: 'eway_bill_number', sortable: true },
    { title: 'Bill No.', key: 'bill', sortable: false },
    { title: 'Party', key: 'party', sortable: false },
    { title: 'Vehicle No.', key: 'vehicle_number', sortable: false },
    { title: 'Generated', key: 'generated_at', sortable: true },
    { title: 'Valid Until', key: 'valid_until', sortable: true },
    { title: 'Transport', key: 'transport_mode', sortable: false },
    { title: 'Actions', key: 'actions', sortable: false, align: 'center', width: 120 },
];

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const isExpired = (date) => {
    return new Date(date) < new Date();
};

let searchTimeout = null;
const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadEwayBills(1);
    }, 300);
};

const loadEwayBills = async (page = 1) => {
    try {
        await billStore.fetchEwayBills(page, filters.value);
    } catch (error) {
        showSnackbar('Failed to load e-way bills', 'error');
    }
};

const onPageChange = (page) => {
    loadEwayBills(page);
};

const onItemsPerPageChange = () => {
    loadEwayBills(1);
};

const clearFilters = () => {
    filters.value = {
        search: '',
        status: null,
        date: '',
    };
    loadEwayBills(1);
};

const downloadPdf = async (id) => {
    try {
        await billStore.downloadEwayBillPdf(id);
        showSnackbar('PDF downloaded successfully!', 'success');
    } catch (error) {
        showSnackbar('Failed to download PDF', 'error');
    }
};

onMounted(() => {
    loadEwayBills();
});
</script>
