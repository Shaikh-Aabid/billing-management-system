<template>
    <div>
        <!-- Welcome Header -->
        <div class="d-flex justify-space-between align-center mb-6">
            <div>
                <h1 class="text-h4 font-weight-bold text-on-background">Dashboard</h1>
                <p class="text-body-2 text-medium-emphasis mt-1">Welcome back! Here's your business overview.</p>
            </div>
            <v-btn
                color="primary"
                prepend-icon="mdi-plus"
                to="/bills/create"
                size="large"
            >
                Create New Bill
            </v-btn>
        </div>

        <!-- Stats Cards -->
        <v-row class="mb-2">
            <v-col cols="12" sm="6" lg="3">
                <v-card class="stat-card overflow-hidden" :loading="loading">
                    <div class="stat-card-gradient stat-card-primary"></div>
                    <v-card-text class="pa-5">
                        <div class="d-flex justify-space-between align-start">
                            <div>
                                <p class="text-caption text-medium-emphasis text-uppercase font-weight-medium mb-1">Total Bills</p>
                                <h3 class="text-h4 font-weight-bold">{{ stats.totalBills }}</h3>
                                <div class="d-flex align-center mt-2">
                                    <v-icon size="16" color="primary" class="mr-1">mdi-receipt-text</v-icon>
                                    <span class="text-caption text-medium-emphasis">All time</span>
                                </div>
                            </div>
                            <v-avatar color="primary" variant="tonal" size="48" rounded="lg">
                                <v-icon size="24">mdi-receipt-text-outline</v-icon>
                            </v-avatar>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col cols="12" sm="6" lg="3">
                <v-card class="stat-card overflow-hidden" :loading="loading">
                    <div class="stat-card-gradient stat-card-success"></div>
                    <v-card-text class="pa-5">
                        <div class="d-flex justify-space-between align-start">
                            <div>
                                <p class="text-caption text-medium-emphasis text-uppercase font-weight-medium mb-1">Total Revenue</p>
                                <h3 class="text-h4 font-weight-bold">{{ formatCurrency(stats.totalAmount) }}</h3>
                                <div class="d-flex align-center mt-2">
                                    <v-icon size="16" color="success" class="mr-1">mdi-currency-inr</v-icon>
                                    <span class="text-caption text-medium-emphasis">All time</span>
                                </div>
                            </div>
                            <v-avatar color="success" variant="tonal" size="48" rounded="lg">
                                <v-icon size="24">mdi-currency-inr</v-icon>
                            </v-avatar>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col cols="12" sm="6" lg="3">
                <v-card class="stat-card overflow-hidden" :loading="loading">
                    <div class="stat-card-gradient stat-card-warning"></div>
                    <v-card-text class="pa-5">
                        <div class="d-flex justify-space-between align-start">
                            <div>
                                <p class="text-caption text-medium-emphasis text-uppercase font-weight-medium mb-1">GST Collected</p>
                                <h3 class="text-h4 font-weight-bold">{{ formatCurrency(stats.totalGst) }}</h3>
                                <div class="d-flex align-center mt-2">
                                    <v-icon size="16" color="warning" class="mr-1">mdi-information-outline</v-icon>
                                    <span class="text-caption text-medium-emphasis">CGST + SGST + IGST</span>
                                </div>
                            </div>
                            <v-avatar color="warning" variant="tonal" size="48" rounded="lg">
                                <v-icon size="24">mdi-percent-outline</v-icon>
                            </v-avatar>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col cols="12" sm="6" lg="3">
                <v-card class="stat-card overflow-hidden" :loading="loading">
                    <div class="stat-card-gradient stat-card-info"></div>
                    <v-card-text class="pa-5">
                        <div class="d-flex justify-space-between align-start">
                            <div>
                                <p class="text-caption text-medium-emphasis text-uppercase font-weight-medium mb-1">This Month</p>
                                <h3 class="text-h4 font-weight-bold">{{ stats.monthlyBills }}</h3>
                                <div class="d-flex align-center mt-2">
                                    <v-icon size="16" color="info" class="mr-1">mdi-calendar-check</v-icon>
                                    <span class="text-caption text-medium-emphasis">{{ currentMonth }}</span>
                                </div>
                            </div>
                            <v-avatar color="info" variant="tonal" size="48" rounded="lg">
                                <v-icon size="24">mdi-calendar-month-outline</v-icon>
                            </v-avatar>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Charts Row -->
        <v-row class="mb-2">
            <!-- Revenue Trend Chart -->
            <v-col cols="12" lg="8">
                <v-card :loading="loading">
                    <v-card-title class="d-flex justify-space-between align-center pa-5 pb-0">
                        <div>
                            <span class="font-weight-bold">Revenue Overview</span>
                            <p class="text-caption text-medium-emphasis mt-1 mb-0">Monthly revenue trend for {{ currentYear }}</p>
                        </div>
                    </v-card-title>
                    <v-card-text class="pa-5">
                        <div v-if="hasRevenueData">
                            <apexchart
                                type="area"
                                height="320"
                                :options="revenueChartOptions"
                                :series="revenueSeries"
                            ></apexchart>
                        </div>
                        <div v-else class="text-center py-12">
                            <v-icon size="64" color="grey-lighten-1" class="mb-4">mdi-chart-line</v-icon>
                            <h3 class="text-h6 mb-2">No Revenue Data Yet</h3>
                            <p class="text-body-2 text-medium-emphasis">Create bills to see your revenue trends here</p>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- GST Breakdown Pie Chart -->
            <v-col cols="12" lg="4">
                <v-card class="h-100" :loading="loading">
                    <v-card-title class="pa-5 pb-0">
                        <span class="font-weight-bold">GST Breakdown</span>
                        <p class="text-caption text-medium-emphasis mt-1 mb-0">Distribution of tax collected</p>
                    </v-card-title>
                    <v-card-text class="pa-5 d-flex flex-column align-center">
                        <div v-if="stats.totalGst > 0">
                            <apexchart
                                type="donut"
                                width="280"
                                :options="gstChartOptions"
                                :series="gstSeries"
                            ></apexchart>
                            <div class="d-flex justify-space-around w-100 mt-4">
                                <div class="text-center">
                                    <v-chip color="primary" size="small" variant="flat" class="mb-1">CGST</v-chip>
                                    <p class="text-body-2 font-weight-bold mb-0">{{ formatCurrency(stats.totalCgst) }}</p>
                                </div>
                                <div class="text-center">
                                    <v-chip color="secondary" size="small" variant="flat" class="mb-1">SGST</v-chip>
                                    <p class="text-body-2 font-weight-bold mb-0">{{ formatCurrency(stats.totalSgst) }}</p>
                                </div>
                                <div class="text-center" v-if="stats.totalIgst > 0">
                                    <v-chip color="accent" size="small" variant="flat" class="mb-1">IGST</v-chip>
                                    <p class="text-body-2 font-weight-bold mb-0">{{ formatCurrency(stats.totalIgst) }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <v-icon size="48" color="grey-lighten-1" class="mb-3">mdi-chart-pie</v-icon>
                            <p class="text-body-2 text-medium-emphasis">No GST data available</p>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Quick Actions & Recent Bills -->
        <v-row class="mb-2">
            <!-- Quick Actions -->
            <v-col cols="12" md="4">
                <v-card>
                    <v-card-title class="pa-5 pb-3">
                        <span class="font-weight-bold">Quick Actions</span>
                    </v-card-title>
                    <v-card-text class="pa-5 pt-0">
                        <v-list class="pa-0">
                            <v-list-item
                                prepend-icon="mdi-receipt-text-plus"
                                title="Create New Bill"
                                subtitle="Generate GST invoice"
                                to="/bills/create"
                                rounded="lg"
                                class="mb-2 bg-primary-subtle"
                            >
                                <template v-slot:append>
                                    <v-icon size="small">mdi-chevron-right</v-icon>
                                </template>
                            </v-list-item>
                            <v-list-item
                                prepend-icon="mdi-account-plus-outline"
                                title="Add New Party"
                                subtitle="Register customer/vendor"
                                @click="showPartyDialog = true"
                                rounded="lg"
                                class="mb-2 bg-secondary-subtle"
                            >
                                <template v-slot:append>
                                    <v-icon size="small">mdi-chevron-right</v-icon>
                                </template>
                            </v-list-item>
                            <v-list-item
                                prepend-icon="mdi-package-variant-closed-plus"
                                title="Add New Product"
                                subtitle="Add inventory item"
                                @click="showProductDialog = true"
                                rounded="lg"
                                class="mb-2 bg-success-subtle"
                            >
                                <template v-slot:append>
                                    <v-icon size="small">mdi-chevron-right</v-icon>
                                </template>
                            </v-list-item>
                            <v-list-item
                                prepend-icon="mdi-truck-outline"
                                title="E-Way Bill"
                                subtitle="Generate transport doc"
                                to="/eway-bills"
                                rounded="lg"
                                class="bg-info-subtle"
                            >
                                <template v-slot:append>
                                    <v-icon size="small">mdi-chevron-right</v-icon>
                                </template>
                            </v-list-item>
                        </v-list>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Recent Bills -->
            <v-col cols="12" md="8">
                <v-card :loading="loading">
                    <v-card-title class="d-flex justify-space-between align-center pa-5 pb-3">
                        <div>
                            <span class="font-weight-bold">Recent Bills</span>
                            <p class="text-caption text-medium-emphasis mt-1 mb-0">Latest invoices generated</p>
                        </div>
                        <v-btn variant="text" color="primary" to="/bills" size="small">
                            View All
                            <v-icon end size="small">mdi-arrow-right</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-card-text class="pa-5 pt-0">
                        <v-table v-if="recentBills.length > 0" density="comfortable" class="rounded-lg">
                            <thead>
                                <tr>
                                    <th class="text-left">Invoice</th>
                                    <th class="text-left">Party</th>
                                    <th class="text-left">Date</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="bill in recentBills" :key="bill.id" class="cursor-pointer" @click="$router.push(`/bills/${bill.id}`)">
                                    <td>
                                        <div class="d-flex align-center">
                                            <v-avatar color="primary" variant="tonal" size="32" class="mr-2">
                                                <v-icon size="16">mdi-receipt-text</v-icon>
                                            </v-avatar>
                                            <span class="font-weight-medium">{{ bill.bill_number }}</span>
                                        </div>
                                    </td>
                                    <td>{{ bill.party?.name || 'N/A' }}</td>
                                    <td class="text-medium-emphasis">{{ formatDate(bill.bill_date) }}</td>
                                    <td class="text-right font-weight-bold text-success">{{ formatCurrency(bill.total_amount) }}</td>
                                </tr>
                            </tbody>
                        </v-table>
                        <v-alert v-else type="info" variant="tonal" rounded="lg" class="mb-0">
                            <div class="d-flex align-center">
                                <v-icon class="mr-2">mdi-information-outline</v-icon>
                                <div>
                                    <strong>No bills yet!</strong>
                                    <p class="text-body-2 mb-0">Create your first invoice to get started.</p>
                                </div>
                            </div>
                        </v-alert>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Monthly Billing Bar Chart & Top Customers -->
        <v-row>
            <v-col cols="12" md="8">
                <v-card :loading="loading">
                    <v-card-title class="d-flex justify-space-between align-center pa-5 pb-0">
                        <div>
                            <span class="font-weight-bold">Monthly Billing Activity</span>
                            <p class="text-caption text-medium-emphasis mt-1 mb-0">Number of bills generated per month in {{ currentYear }}</p>
                        </div>
                    </v-card-title>
                    <v-card-text class="pa-5">
                        <div v-if="hasBillingData">
                            <apexchart
                                type="bar"
                                height="280"
                                :options="billingChartOptions"
                                :series="billingSeries"
                            ></apexchart>
                        </div>
                        <div v-else class="text-center py-12">
                            <v-icon size="64" color="grey-lighten-1" class="mb-4">mdi-chart-bar</v-icon>
                            <h3 class="text-h6 mb-2">No Billing Activity Yet</h3>
                            <p class="text-body-2 text-medium-emphasis">Create bills to see your monthly activity here</p>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Top Customers -->
            <v-col cols="12" md="4">
                <v-card :loading="loading">
                    <v-card-title class="pa-5 pb-3">
                        <span class="font-weight-bold">Top Customers</span>
                        <p class="text-caption text-medium-emphasis mt-1 mb-0">By revenue</p>
                    </v-card-title>
                    <v-card-text class="pa-5 pt-0">
                        <v-list v-if="topParties.length > 0" class="pa-0">
                            <v-list-item
                                v-for="(party, index) in topParties"
                                :key="index"
                                rounded="lg"
                                class="mb-2"
                            >
                                <template v-slot:prepend>
                                    <v-avatar :color="getPartyColor(index)" variant="tonal" size="40">
                                        <span class="font-weight-bold">{{ index + 1 }}</span>
                                    </v-avatar>
                                </template>
                                <v-list-item-title class="font-weight-medium">{{ party.name }}</v-list-item-title>
                                <v-list-item-subtitle>{{ party.bills }} bills</v-list-item-subtitle>
                                <template v-slot:append>
                                    <span class="text-success font-weight-bold">{{ formatCurrency(party.revenue) }}</span>
                                </template>
                            </v-list-item>
                        </v-list>
                        <div v-else class="text-center py-8">
                            <v-icon size="48" color="grey-lighten-1" class="mb-3">mdi-account-group</v-icon>
                            <p class="text-body-2 text-medium-emphasis">No customer data yet</p>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Add Party Dialog -->
        <PartyDialog
            v-model="showPartyDialog"
            @saved="onPartySaved"
        />

        <!-- Add Product Dialog -->
        <ProductDialog
            v-model="showProductDialog"
            @saved="onProductSaved"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue';
import { useBillStore } from '../stores/bill';
import PartyDialog from '../components/PartyDialog.vue';
import ProductDialog from '../components/ProductDialog.vue';

const billStore = useBillStore();
const showSnackbar = inject('showSnackbar');

const loading = ref(true);
const stats = ref({
    totalBills: 0,
    totalAmount: 0,
    totalGst: 0,
    totalCgst: 0,
    totalSgst: 0,
    totalIgst: 0,
    monthlyBills: 0,
});

const chartData = ref({
    months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    revenue: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    gst: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    billCount: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
});

const recentBills = ref([]);
const topParties = ref([]);
const showPartyDialog = ref(false);
const showProductDialog = ref(false);

const currentYear = computed(() => new Date().getFullYear());

const currentMonth = computed(() => {
    return new Date().toLocaleString('en-IN', { month: 'long', year: 'numeric' });
});

const hasRevenueData = computed(() => {
    return chartData.value.revenue.some(v => v > 0);
});

const hasBillingData = computed(() => {
    return chartData.value.billCount.some(v => v > 0);
});

// Revenue Chart Options
const revenueChartOptions = computed(() => ({
    chart: {
        type: 'area',
        toolbar: { show: false },
        fontFamily: 'inherit',
        sparkline: { enabled: false },
    },
    colors: ['#6366F1', '#10B981'],
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.1,
            stops: [0, 90, 100]
        }
    },
    stroke: {
        curve: 'smooth',
        width: 3,
    },
    dataLabels: { enabled: false },
    xaxis: {
        categories: chartData.value.months,
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: {
            style: { colors: '#94A3B8', fontSize: '12px' }
        }
    },
    yaxis: {
        labels: {
            style: { colors: '#94A3B8', fontSize: '12px' },
            formatter: (val) => {
                if (val >= 100000) return '₹' + (val / 100000).toFixed(1) + 'L';
                if (val >= 1000) return '₹' + (val / 1000).toFixed(0) + 'K';
                return '₹' + val;
            }
        }
    },
    grid: {
        borderColor: '#E2E8F0',
        strokeDashArray: 4,
    },
    legend: {
        position: 'top',
        horizontalAlign: 'right',
    },
    tooltip: {
        y: {
            formatter: (val) => '₹' + val.toLocaleString('en-IN')
        }
    }
}));

const revenueSeries = computed(() => [
    {
        name: 'Revenue',
        data: chartData.value.revenue
    },
    {
        name: 'GST',
        data: chartData.value.gst
    }
]);

// GST Breakdown Chart Options
const gstChartOptions = computed(() => {
    const labels = ['CGST', 'SGST'];
    if (stats.value.totalIgst > 0) labels.push('IGST');

    return {
        chart: {
            type: 'donut',
            fontFamily: 'inherit',
        },
        colors: stats.value.totalIgst > 0 ? ['#6366F1', '#8B5CF6', '#EC4899'] : ['#6366F1', '#8B5CF6'],
        labels: labels,
        legend: { show: false },
        dataLabels: { enabled: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name: { show: true },
                        value: {
                            show: true,
                            formatter: (val) => '₹' + Number(val).toLocaleString('en-IN')
                        },
                        total: {
                            show: true,
                            label: 'Total GST',
                            formatter: () => formatCurrency(stats.value.totalGst)
                        }
                    }
                }
            }
        },
        stroke: { width: 0 },
    };
});

const gstSeries = computed(() => {
    const series = [stats.value.totalCgst || 0, stats.value.totalSgst || 0];
    if (stats.value.totalIgst > 0) series.push(stats.value.totalIgst);
    return series;
});

// Billing Activity Chart Options
const billingChartOptions = computed(() => ({
    chart: {
        type: 'bar',
        toolbar: { show: false },
        fontFamily: 'inherit',
    },
    colors: ['#6366F1'],
    plotOptions: {
        bar: {
            borderRadius: 8,
            columnWidth: '50%',
            distributed: false,
        }
    },
    dataLabels: { enabled: false },
    xaxis: {
        categories: chartData.value.months,
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: {
            style: { colors: '#94A3B8', fontSize: '12px' }
        }
    },
    yaxis: {
        labels: {
            style: { colors: '#94A3B8', fontSize: '12px' }
        }
    },
    grid: {
        borderColor: '#E2E8F0',
        strokeDashArray: 4,
    },
    tooltip: {
        y: {
            formatter: (val) => val + ' bills'
        }
    }
}));

const billingSeries = computed(() => [
    {
        name: 'Bills Generated',
        data: chartData.value.billCount
    }
]);

const getPartyColor = (index) => {
    const colors = ['primary', 'secondary', 'success', 'info', 'warning'];
    return colors[index % colors.length];
};

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

const onPartySaved = () => {
    showSnackbar('Party added successfully!', 'success');
};

const onProductSaved = () => {
    showSnackbar('Product added successfully!', 'success');
};

const loadDashboardData = async () => {
    loading.value = true;
    try {
        const data = await billStore.fetchStats();

        // Update stats
        stats.value = {
            totalBills: data.totalBills || 0,
            totalAmount: data.totalAmount || 0,
            totalGst: data.totalGst || 0,
            totalCgst: data.totalCgst || 0,
            totalSgst: data.totalSgst || 0,
            totalIgst: data.totalIgst || 0,
            monthlyBills: data.monthlyBills || 0,
        };

        // Update chart data
        if (data.chartData) {
            chartData.value = data.chartData;
        }

        // Update recent bills
        if (data.recentBills) {
            recentBills.value = data.recentBills;
        }

        // Update top parties
        if (data.topParties) {
            topParties.value = data.topParties;
        }
    } catch (error) {
        console.error('Failed to load dashboard data:', error);
        showSnackbar('Failed to load dashboard data', 'error');
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadDashboardData();
});
</script>

<style scoped>
.stat-card {
    position: relative;
}

.stat-card-gradient {
    position: absolute;
    top: 0;
    right: 0;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    filter: blur(40px);
    opacity: 0.15;
    transform: translate(30%, -30%);
}

.stat-card-primary {
    background: #6366F1;
}

.stat-card-success {
    background: #10B981;
}

.stat-card-warning {
    background: #F59E0B;
}

.stat-card-info {
    background: #3B82F6;
}

.bg-primary-subtle {
    background: rgba(99, 102, 241, 0.08) !important;
}

.bg-secondary-subtle {
    background: rgba(139, 92, 246, 0.08) !important;
}

.bg-success-subtle {
    background: rgba(16, 185, 129, 0.08) !important;
}

.bg-info-subtle {
    background: rgba(59, 130, 246, 0.08) !important;
}

.cursor-pointer {
    cursor: pointer;
}

.cursor-pointer:hover {
    background: rgba(99, 102, 241, 0.04);
}

.h-100 {
    height: 100%;
}

.w-100 {
    width: 100%;
}
</style>
