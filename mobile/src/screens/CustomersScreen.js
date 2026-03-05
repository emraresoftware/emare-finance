import React, { useState, useCallback, useRef, useEffect } from 'react';
import { View, FlatList, StyleSheet, RefreshControl } from 'react-native';
import { useFocusEffect } from '@react-navigation/native';
import * as Haptics from 'expo-haptics';
import { Colors, Spacing } from '../theme';
import api from '../api/client';
import SearchBar from '../components/SearchBar';
import CustomerCard from '../components/CustomerCard';
import LoadingState from '../components/LoadingState';
import EmptyState from '../components/EmptyState';

export default function CustomersScreen({ navigation }) {
  const [customers, setCustomers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);
  const searchTimeout = useRef(null);

  const fetchCustomers = async (pageNum = 1, isRefresh = false) => {
    try {
      const params = { page: pageNum, per_page: 20 };
      if (search) params.search = search;

      const result = await api.getCustomers(params);
      // API doğrudan pagination döndürür
      const paginatedData = result.data || result.customers?.data || result.customers || [];
      const newCustomers = Array.isArray(paginatedData) ? paginatedData : [];

      if (isRefresh || pageNum === 1) {
        setCustomers(newCustomers);
      } else {
        setCustomers(prev => [...prev, ...newCustomers]);
      }
      setHasMore(newCustomers.length >= 20);
      setPage(pageNum);
    } catch (error) {
      console.error('Müşteriler yüklenemedi:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
      setLoadingMore(false);
    }
  };

  useFocusEffect(
    useCallback(() => {
      fetchCustomers(1, true);
    }, [])
  );

  useEffect(() => {
    if (searchTimeout.current) clearTimeout(searchTimeout.current);
    searchTimeout.current = setTimeout(() => {
      setLoading(true);
      setPage(1);
      fetchCustomers(1, true);
    }, 500);
    return () => clearTimeout(searchTimeout.current);
  }, [search]);

  const onRefresh = () => {
    Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Light);
    setRefreshing(true);
    fetchCustomers(1, true);
  };

  const loadMore = () => {
    if (!hasMore || loadingMore) return;
    setLoadingMore(true);
    fetchCustomers(page + 1);
  };

  const renderCustomer = ({ item }) => (
    <CustomerCard
      customer={item}
      onPress={() => navigation.navigate('CustomerDetail', { customerId: item.id })}
    />
  );

  return (
    <View style={styles.container}>
      <SearchBar
        value={search}
        onChangeText={setSearch}
        placeholder="Müşteri ara (ad, telefon)..."
      />

      {loading ? (
        <LoadingState message="Müşteriler yükleniyor..." />
      ) : (
        <FlatList
          data={customers}
          renderItem={renderCustomer}
          keyExtractor={item => String(item.id)}
          contentContainerStyle={styles.list}
          showsVerticalScrollIndicator={false}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[Colors.primary]} />
          }
          onEndReached={loadMore}
          onEndReachedThreshold={0.3}
          ListEmptyComponent={
            <EmptyState icon="people-outline" title="Müşteri bulunamadı" subtitle="Arama kriterlerinizi değiştirmeyi deneyin" />
          }
        />
      )}
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
});
