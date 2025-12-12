<template>
    <div>
        <div class="d-flex justify-space-between align-center mb-6">
            <h1 class="text-h4">Products</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog()">
                Add Product
            </v-btn>
        </div>

        <!-- Search and Filters -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="search"
                            label="Search products..."
                            prepend-inner-icon="mdi-magnify"
                            clearable
                            hide-details
                            @input="debouncedSearch"
                        ></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Products Table -->
        <v-card>
            <v-data-table-server
                :headers="headers"
                :items="productStore.products"
                :items-length="productStore.pagination.total"
                :loading="productStore.loading"
                :page="productStore.pagination.currentPage"
                :items-per-page="productStore.pagination.perPage"
                @update:page="onPageChange"
                @update:items-per-page="onItemsPerPageChange"
            >
                <template v-slot:item.hsn_code="{ item }">
                    <v-chip size="small" color="info" variant="tonal">
                        {{ item.hsn_code }}
                    </v-chip>
                </template>

                <template v-slot:item.price="{ item }">
                    {{ formatCurrency(item.price) }}
                </template>

                <template v-slot:item.gst_rate="{ item }">
                    <v-chip size="small" color="warning" variant="tonal">
                        {{ item.gst_rate }}%
                    </v-chip>
                </template>

                <template v-slot:item.actions="{ item }">
                    <div class="d-flex justify-center ga-1">
                        <v-tooltip text="Edit Product" location="top">
                            <template v-slot:activator="{ props }">
                                <v-btn
                                    v-bind="props"
                                    icon="mdi-pencil"
                                    variant="text"
                                    size="small"
                                    color="primary"
                                    @click="openDialog(item)"
                                ></v-btn>
                            </template>
                        </v-tooltip>
                        <v-tooltip text="Delete Product" location="top">
                            <template v-slot:activator="{ props }">
                                <v-btn
                                    v-bind="props"
                                    icon="mdi-delete"
                                    variant="text"
                                    size="small"
                                    color="error"
                                    @click="confirmDelete(item)"
                                ></v-btn>
                            </template>
                        </v-tooltip>
                    </div>
                </template>
            </v-data-table-server>
        </v-card>

        <!-- Product Dialog -->
        <ProductDialog
            v-model="showDialog"
            :product="selectedProduct"
            @saved="onProductSaved"
        />

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="showDeleteDialog" max-width="400">
            <v-card>
                <v-card-title>Confirm Delete</v-card-title>
                <v-card-text>
                    Are you sure you want to delete "{{ productToDelete?.name }}"?
                    This action cannot be undone.
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="showDeleteDialog = false">Cancel</v-btn>
                    <v-btn
                        color="error"
                        :loading="productStore.loading"
                        @click="deleteProduct"
                    >
                        Delete
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue';
import { useProductStore } from '../stores/product';
import ProductDialog from '../components/ProductDialog.vue';

const productStore = useProductStore();
const showSnackbar = inject('showSnackbar');

const search = ref('');
const showDialog = ref(false);
const showDeleteDialog = ref(false);
const selectedProduct = ref(null);
const productToDelete = ref(null);

const headers = [
    { title: 'Name', key: 'name', sortable: true },
    { title: 'HSN Code', key: 'hsn_code', sortable: true },
    { title: 'Unit', key: 'unit', sortable: false },
    { title: 'Price', key: 'price', sortable: true },
    { title: 'GST Rate', key: 'gst_rate', sortable: true },
    { title: 'Actions', key: 'actions', sortable: false, align: 'center', width: 120 },
];

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
    }).format(amount || 0);
};

let searchTimeout = null;
const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadProducts(1);
    }, 300);
};

const loadProducts = async (page = 1) => {
    try {
        await productStore.fetchProducts(page, search.value);
    } catch (error) {
        showSnackbar('Failed to load products', 'error');
    }
};

const onPageChange = (page) => {
    loadProducts(page);
};

const onItemsPerPageChange = () => {
    loadProducts(1);
};

const openDialog = (product = null) => {
    selectedProduct.value = product;
    showDialog.value = true;
};

const onProductSaved = () => {
    showSnackbar(
        selectedProduct.value ? 'Product updated successfully!' : 'Product created successfully!',
        'success'
    );
    loadProducts(productStore.pagination.currentPage);
};

const confirmDelete = (product) => {
    productToDelete.value = product;
    showDeleteDialog.value = true;
};

const deleteProduct = async () => {
    try {
        await productStore.deleteProduct(productToDelete.value.id);
        showDeleteDialog.value = false;
        showSnackbar('Product deleted successfully!', 'success');
    } catch (error) {
        showSnackbar('Failed to delete product', 'error');
    }
};

onMounted(() => {
    loadProducts();
});
</script>
