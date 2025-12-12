import { defineStore } from 'pinia';
import axios from 'axios';

export const useProductStore = defineStore('product', {
    state: () => ({
        products: [],
        currentProduct: null,
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
        getProductById: (state) => (id) => {
            return state.products.find((product) => product.id === id);
        },
    },

    actions: {
        async fetchProducts(page = 1, search = '') {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get('/products', {
                    params: { page, search },
                });
                this.products = response.data.data;
                this.pagination = {
                    currentPage: response.data.current_page,
                    lastPage: response.data.last_page,
                    perPage: response.data.per_page,
                    total: response.data.total,
                };
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch products';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchAllProducts() {
            try {
                const response = await axios.get('/products/all');
                return response.data;
            } catch (error) {
                throw error;
            }
        },

        async fetchProduct(id) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get(`/products/${id}`);
                this.currentProduct = response.data;
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch product';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async createProduct(productData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.post('/products', productData);
                this.products.unshift(response.data);
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to create product';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateProduct(id, productData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.put(`/products/${id}`, productData);
                const index = this.products.findIndex((p) => p.id === id);
                if (index !== -1) {
                    this.products[index] = response.data;
                }
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to update product';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteProduct(id) {
            this.loading = true;
            this.error = null;
            try {
                await axios.delete(`/products/${id}`);
                this.products = this.products.filter((p) => p.id !== id);
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to delete product';
                throw error;
            } finally {
                this.loading = false;
            }
        },
    },
});
