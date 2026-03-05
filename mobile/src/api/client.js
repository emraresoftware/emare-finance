import AsyncStorage from '@react-native-async-storage/async-storage';

const DEFAULT_URL = 'http://192.168.1.100:8000';

class ApiClient {
  constructor() {
    this.baseUrl = DEFAULT_URL;
    this._loaded = false;
  }

  async init() {
    if (this._loaded) return;
    const saved = await AsyncStorage.getItem('api_url');
    if (saved) this.baseUrl = saved;
    this._loaded = true;
  }

  async setBaseUrl(url) {
    // Sondaki slash'ı kaldır
    url = url.replace(/\/+$/, '');
    this.baseUrl = url;
    await AsyncStorage.setItem('api_url', url);
  }

  async getBaseUrl() {
    await this.init();
    return this.baseUrl;
  }

  async request(endpoint, options = {}) {
    await this.init();

    const url = `${this.baseUrl}/api${endpoint}`;
    const config = {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      ...options,
    };

    try {
      const response = await fetch(url, config);

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 200)}`);
      }

      return await response.json();
    } catch (error) {
      if (error.message.includes('Network request failed')) {
        throw new Error('Sunucuya bağlanılamıyor. Lütfen bağlantınızı kontrol edin.');
      }
      throw error;
    }
  }

  // Dashboard
  async getDashboard() {
    return this.request('/dashboard');
  }

  // Ürünler
  async getProducts(params = {}) {
    const query = new URLSearchParams(params).toString();
    return this.request(`/products?${query}`);
  }

  async getProduct(id) {
    return this.request(`/products/${id}`);
  }

  async getLowStockProducts() {
    return this.request('/products/low-stock');
  }

  async getCategories() {
    return this.request('/products/categories');
  }

  // Satışlar
  async getSales(params = {}) {
    const query = new URLSearchParams(params).toString();
    return this.request(`/sales?${query}`);
  }

  async getSale(id) {
    return this.request(`/sales/${id}`);
  }

  async getSalesSummary(params = {}) {
    const query = new URLSearchParams(params).toString();
    return this.request(`/sales/summary?${query}`);
  }

  // Cariler
  async getCustomers(params = {}) {
    const query = new URLSearchParams(params).toString();
    return this.request(`/customers?${query}`);
  }

  async getCustomer(id) {
    return this.request(`/customers/${id}`);
  }

  async getCustomerSales(id, params = {}) {
    const query = new URLSearchParams(params).toString();
    return this.request(`/customers/${id}/sales?${query}`);
  }

  // Raporlar
  async getDailyReport(date) {
    return this.request(`/reports/daily?date=${date || ''}`);
  }

  async getTopProducts(params = {}) {
    const days = typeof params === 'object' ? params.days : params;
    return this.request(`/reports/top-products?days=${days || 30}`);
  }

  async getRevenueChart(params = {}) {
    const days = typeof params === 'object' ? params.days : params;
    return this.request(`/reports/revenue-chart?days=${days || 30}`);
  }

  async getPaymentMethods(params = {}) {
    const days = typeof params === 'object' ? params.days : params;
    return this.request(`/reports/payment-methods?days=${days || 30}`);
  }

  // Stok
  async getStockOverview() {
    return this.request('/stock/overview');
  }

  async getStockMovements(params = {}) {
    const query = new URLSearchParams(params).toString();
    return this.request(`/stock/movements?${query}`);
  }

  async getStockAlerts() {
    return this.request('/stock/alerts');
  }

  // Bağlantı testi
  async testConnection() {
    try {
      const data = await this.getDashboard();
      return { success: true, data };
    } catch (error) {
      return { success: false, error: error.message };
    }
  }
}

export default new ApiClient();
