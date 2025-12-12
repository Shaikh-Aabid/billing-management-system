import { defineStore } from 'pinia';
import axios from 'axios';

export const usePartyStore = defineStore('party', {
    state: () => ({
        parties: [],
        currentParty: null,
        loading: false,
        error: null,
        pagination: {
            currentPage: 1,
            lastPage: 1,
            perPage: 10,
            total: 0,
        },
    }),

    getters: {
        getPartyById: (state) => (id) => {
            return state.parties.find((party) => party.id === id);
        },
    },

    actions: {
        async fetchParties(page = 1, search = '') {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get('/parties', {
                    params: { page, search },
                });
                this.parties = response.data.data;
                this.pagination = {
                    currentPage: response.data.current_page,
                    lastPage: response.data.last_page,
                    perPage: response.data.per_page,
                    total: response.data.total,
                };
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch parties';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchAllParties() {
            try {
                const response = await axios.get('/parties/all');
                return response.data;
            } catch (error) {
                throw error;
            }
        },

        async fetchParty(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(`/parties/${id}`);
                this.currentParty = response.data;
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch party';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async createParty(partyData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post('/parties', partyData);
                this.parties.unshift(response.data);
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to create party';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateParty(id, partyData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.put(`/parties/${id}`, partyData);
                const index = this.parties.findIndex((p) => p.id === id);
                if (index !== -1) {
                    this.parties[index] = response.data;
                }
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to update party';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteParty(id) {
            this.loading = true;
            this.error = null;
            try {
                await axios.delete(`/parties/${id}`);
                this.parties = this.parties.filter((p) => p.id !== id);
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to delete party';
                throw error;
            } finally {
                this.loading = false;
            }
        },
    },
});
