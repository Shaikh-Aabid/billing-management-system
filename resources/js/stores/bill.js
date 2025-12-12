import { defineStore } from 'pinia';
import axios from 'axios';

export const useBillStore = defineStore('bill', {
    state: () => ({
        bills: [],
        currentBill: null,
        ewayBills: [],
        loading: false,
        error: null,
        pagination: {
            currentPage: 1,
            lastPage: 1,
            perPage: 10,
            total: 0,
        },
        stats: {
            totalBills: 0,
            totalAmount: 0,
            totalGst: 0,
            monthlyBills: 0,
        },
    }),

    getters: {
        getBillById: (state) => (id) => {
            return state.bills.find((bill) => bill.id === id);
        },
    },

    actions: {
        async fetchBills(page = 1, filters = {}) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get('/bills', {
                    params: { page, ...filters },
                });
                this.bills = response.data.data;
                this.pagination = {
                    currentPage: response.data.current_page,
                    lastPage: response.data.last_page,
                    perPage: response.data.per_page,
                    total: response.data.total,
                };
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch bills';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchBill(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(`/bills/${id}`);
                this.currentBill = response.data;
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch bill';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async createBill(billData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post('/bills', billData);
                this.bills.unshift(response.data);
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to create bill';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateBill(id, billData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.put(`/bills/${id}`, billData);
                const index = this.bills.findIndex((b) => b.id === id);
                if (index !== -1) {
                    this.bills[index] = response.data;
                }
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to update bill';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteBill(id) {
            this.loading = true;
            this.error = null;
            try {
                await axios.delete(`/bills/${id}`);
                this.bills = this.bills.filter((b) => b.id !== id);
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to delete bill';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async downloadBillPdf(id) {
            try {
                const response = await axios.get(`/bills/${id}/pdf`, {
                    responseType: 'blob',
                });
                const blob = new Blob([response.data], { type: 'application/pdf' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `bill-${id}.pdf`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to download PDF';
                throw error;
            }
        },

        async fetchStats() {
            try {
                const response = await axios.get('/bills/stats');
                this.stats = response.data;
                return response.data;
            } catch (error) {
                throw error;
            }
        },

        // E-Way Bill Actions
        async fetchEwayBills(page = 1, filters = {}) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get('/eway-bills', {
                    params: { page, ...filters },
                });
                this.ewayBills = response.data.data;
                this.pagination = {
                    currentPage: response.data.current_page,
                    lastPage: response.data.last_page,
                    perPage: response.data.per_page,
                    total: response.data.total,
                };
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch e-way bills';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async generateEwayBill(billId, ewayData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post(`/bills/${billId}/eway-bill`, ewayData);
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to generate e-way bill';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async downloadEwayBillPdf(id) {
            try {
                const response = await axios.get(`/eway-bills/${id}/pdf`, {
                    responseType: 'blob',
                });
                const blob = new Blob([response.data], { type: 'application/pdf' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `eway-bill-${id}.pdf`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to download E-Way Bill PDF';
                throw error;
            }
        },
    },
});
