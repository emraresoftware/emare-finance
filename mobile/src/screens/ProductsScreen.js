import React, { useState, useCallback, useRef, useEffect } from 'react';
import { View, FlatList, StyleSheet, TouchableOpacity, Text, Modal, ScrollView } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect } from '@react-navigation/native';
import * as Haptics from 'expo-haptics';
import { Colors, Spacing, BorderRadius, Shadow, FontSize } from '../theme';
import api from '../api/client';
import SearchBar from '../components/SearchBar';
import ProductCard from '../components/ProductCard';
import LoadingState from '../components/LoadingState';
import EmptyState from '../components/EmptyState';

export default function ProductsScreen({ navigation }) {
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [search, setSearch] = useState('');
  const [selectedCategory, setSelectedCategory] = useState(null);
  const [showFilter, setShowFilter] = useState(false);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);
  const searchTimeout = useRef(null);

  const fetchProducts = async (pageNum = 1, isRefresh = false) => {
    try {
      const params = { page: pageNum, per_page: 20 };
      if (search) params.search = search;
      if (selectedCategory) params.category_id = selectedCategory;

      const result = await api.getProducts(params);
      // API doğrudan pagination döndürür veya {products: pagination} olabilir
      const paginatedData = result.data || result.products?.data || result.products || [];
      const newProducts = Array.isArray(paginatedData) ? paginatedData : [];

      if (isRefresh || pageNum === 1) {
        setProducts(newProducts);
      } else {
        setProducts(prev => [...prev, ...newProducts]);
      }
      setHasMore(newProducts.length >= 20);
      setPage(pageNum);
    } catch (error) {
      console.error('Ürünler yüklenemedi:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
      setLoadingMore(false);
    }
  };

  const fetchCategories = async () => {
    try {
      const result = await api.getCategories();
      setCategories(result.categories || []);
    } catch (error) {
      console.error('Kategoriler yüklenemedi:', error);
    }
  };

  useFocusEffect(
    useCallback(() => {
      fetchProducts(1, true);
      fetchCategories();
    }, [search, selectedCategory])
  );

  useEffect(() => {
    if (searchTimeout.current) clearTimeout(searchTimeout.current);
    searchTimeout.current = setTimeout(() => {
      setLoading(true);
      setPage(1);
      fetchProducts(1, true);
    }, 500);
    return () => clearTimeout(searchTimeout.current);
  }, [search]);

  useEffect(() => {
    setLoading(true);
    setPage(1);
    fetchProducts(1, true);
  }, [selectedCategory]);

  const onRefresh = () => {
    Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Light);
    setRefreshing(true);
    fetchProducts(1, true);
  };

  const loadMore = () => {
    if (!hasMore || loadingMore) return;
    setLoadingMore(true);
    fetchProducts(page + 1);
  };

  const renderProduct = ({ item }) => (
    <ProductCard
      product={item}
      onPress={() => navigation.navigate('ProductDetail', { productId: item.id })}
    />
  );

  const clearFilter = () => {
    setSelectedCategory(null);
    setShowFilter(false);
  };

  return (
    <View style={styles.container}>
      <SearchBar
        value={search}
        onChangeText={setSearch}
        placeholder="Ürün ara (ad, barkod)..."
        showFilter
        onFilterPress={() => setShowFilter(true)}
      />

      {selectedCategory && (
        <View style={styles.filterBadge}>
          <Text style={styles.filterText}>
            {categories.find(c => c.id === selectedCategory)?.name || 'Kategori'}
          </Text>
          <TouchableOpacity onPress={clearFilter}>
            <Ionicons name="close-circle" size={18} color={Colors.primary} />
          </TouchableOpacity>
        </View>
      )}

      {loading ? (
        <LoadingState message="Ürünler yükleniyor..." />
      ) : (
        <FlatList
          data={products}
          renderItem={renderProduct}
          keyExtractor={item => String(item.id)}
          contentContainerStyle={styles.list}
          showsVerticalScrollIndicator={false}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[Colors.primary]} />
          }
          onEndReached={loadMore}
          onEndReachedThreshold={0.3}
          ListEmptyComponent={<EmptyState icon="cube-outline" title="Ürün bulunamadı" subtitle="Arama kriterlerinizi değiştirmeyi deneyin" />}
          ListFooterComponent={loadingMore ? (
            <View style={styles.footer}>
              <Text style={styles.footerText}>Daha fazla yükleniyor...</Text>
            </View>
          ) : null}
        />
      )}

      {/* Kategori Filtre Modal */}
      <Modal visible={showFilter} animationType="slide" transparent>
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Kategori Seç</Text>
              <TouchableOpacity onPress={() => setShowFilter(false)}>
                <Ionicons name="close" size={24} color={Colors.text} />
              </TouchableOpacity>
            </View>

            <ScrollView style={styles.modalBody}>
              <TouchableOpacity
                style={[styles.categoryItem, !selectedCategory && styles.categoryItemActive]}
                onPress={() => { setSelectedCategory(null); setShowFilter(false); }}
              >
                <Text style={[styles.categoryText, !selectedCategory && styles.categoryTextActive]}>
                  Tüm Kategoriler
                </Text>
              </TouchableOpacity>
              {categories.map(cat => (
                <TouchableOpacity
                  key={cat.id}
                  style={[styles.categoryItem, selectedCategory === cat.id && styles.categoryItemActive]}
                  onPress={() => { setSelectedCategory(cat.id); setShowFilter(false); }}
                >
                  <Text style={[styles.categoryText, selectedCategory === cat.id && styles.categoryTextActive]}>
                    {cat.name}
                  </Text>
                  <Text style={styles.categoryCount}>{cat.products_count || 0}</Text>
                </TouchableOpacity>
              ))}
            </ScrollView>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.bg,
  },
  list: {
    paddingBottom: Spacing.xl,
  },
  filterBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'flex-start',
    backgroundColor: Colors.primaryLight,
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: BorderRadius.full,
    marginHorizontal: Spacing.lg,
    marginBottom: Spacing.sm,
    gap: 6,
  },
  filterText: {
    fontSize: FontSize.sm,
    fontWeight: '600',
    color: Colors.primary,
  },
  footer: {
    paddingVertical: Spacing.lg,
    alignItems: 'center',
  },
  footerText: {
    fontSize: FontSize.sm,
    color: Colors.textMuted,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'flex-end',
  },
  modalContent: {
    backgroundColor: Colors.card,
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    maxHeight: '70%',
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: Spacing.xl,
    borderBottomWidth: 1,
    borderBottomColor: Colors.border,
  },
  modalTitle: {
    fontSize: FontSize.lg,
    fontWeight: '700',
    color: Colors.text,
  },
  modalBody: {
    padding: Spacing.lg,
  },
  categoryItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: Spacing.md,
    paddingHorizontal: Spacing.lg,
    borderRadius: BorderRadius.md,
    marginBottom: 4,
  },
  categoryItemActive: {
    backgroundColor: Colors.primaryLight,
  },
  categoryText: {
    fontSize: FontSize.md,
    color: Colors.text,
  },
  categoryTextActive: {
    fontWeight: '700',
    color: Colors.primary,
  },
  categoryCount: {
    fontSize: FontSize.sm,
    color: Colors.textMuted,
    backgroundColor: Colors.bg,
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: BorderRadius.full,
  },
});
