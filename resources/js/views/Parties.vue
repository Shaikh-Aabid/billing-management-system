<template>
    <div>
        <div class="d-flex justify-space-between align-center mb-6">
            <h1 class="text-h4">Parties</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog()">
                Add Party
            </v-btn>
        </div>

        <!-- Search and Filters -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="search"
                            label="Search parties..."
                            prepend-inner-icon="mdi-magnify"
                            clearable
                            hide-details
                            @input="debouncedSearch"
                        ></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Parties Table -->
        <v-card>
            <v-data-table-server
                :headers="headers"
                :items="partyStore.parties"
                :items-length="partyStore.pagination.total"
                :loading="partyStore.loading"
                :page="partyStore.pagination.currentPage"
                :items-per-page="partyStore.pagination.perPage"
                @update:page="onPageChange"
                @update:items-per-page="onItemsPerPageChange"
            >
                <template v-slot:item.gstin="{ item }">
                    <v-chip v-if="item.gstin" size="small" color="primary" variant="tonal">
                        {{ item.gstin }}
                    </v-chip>
                    <span v-else class="text-grey">N/A</span>
                </template>

                <template v-slot:item.contact_number="{ item }">
                    <a v-if="item.contact_number" :href="`tel:${item.contact_number}`">
                        {{ item.contact_number }}
                    </a>
                    <span v-else class="text-grey">N/A</span>
                </template>

                <template v-slot:item.actions="{ item }">
                    <div class="d-flex justify-center ga-1">
                        <v-tooltip text="Edit Party" location="top">
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
                        <v-tooltip text="Delete Party" location="top">
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

        <!-- Party Dialog -->
        <PartyDialog
            v-model="showDialog"
            :party="selectedParty"
            @saved="onPartySaved"
        />

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="showDeleteDialog" max-width="400">
            <v-card>
                <v-card-title>Confirm Delete</v-card-title>
                <v-card-text>
                    Are you sure you want to delete "{{ partyToDelete?.name }}"?
                    This action cannot be undone.
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="showDeleteDialog = false">Cancel</v-btn>
                    <v-btn
                        color="error"
                        :loading="partyStore.loading"
                        @click="deleteParty"
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
import { usePartyStore } from '../stores/party';
import PartyDialog from '../components/PartyDialog.vue';

const partyStore = usePartyStore();
const showSnackbar = inject('showSnackbar');

const search = ref('');
const showDialog = ref(false);
const showDeleteDialog = ref(false);
const selectedParty = ref(null);
const partyToDelete = ref(null);

const headers = [
    { title: 'Name', key: 'name', sortable: true },
    { title: 'GSTIN', key: 'gstin', sortable: false },
    { title: 'Address', key: 'address', sortable: false },
    { title: 'Contact', key: 'contact_number', sortable: false },
    { title: 'Actions', key: 'actions', sortable: false, align: 'center', width: 120 },
];

let searchTimeout = null;
const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        loadParties(1);
    }, 300);
};

const loadParties = async (page = 1) => {
    try {
        await partyStore.fetchParties(page, search.value);
    } catch (error) {
        showSnackbar('Failed to load parties', 'error');
    }
};

const onPageChange = (page) => {
    loadParties(page);
};

const onItemsPerPageChange = () => {
    loadParties(1);
};

const openDialog = (party = null) => {
    selectedParty.value = party;
    showDialog.value = true;
};

const onPartySaved = () => {
    showSnackbar(
        selectedParty.value ? 'Party updated successfully!' : 'Party created successfully!',
        'success'
    );
    loadParties(partyStore.pagination.currentPage);
};

const confirmDelete = (party) => {
    partyToDelete.value = party;
    showDeleteDialog.value = true;
};

const deleteParty = async () => {
    try {
        await partyStore.deleteParty(partyToDelete.value.id);
        showDeleteDialog.value = false;
        showSnackbar('Party deleted successfully!', 'success');
    } catch (error) {
        showSnackbar('Failed to delete party', 'error');
    }
};

onMounted(() => {
    loadParties();
});
</script>
