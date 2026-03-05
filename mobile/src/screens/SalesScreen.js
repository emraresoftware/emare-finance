import React, { useState, useCallback, useEffect } from 'react';
import { View, FlatList, StyleSheet, RefreshControl } from 'react-native';
import { useFocusEffect } from '@react-navigation/native';
import * as Haptics from 'expo-haptics';
import { Colors, Spacing } from '../theme';
import api from '../api/client';
import SearchBar from '../components/SearchBar';
import SaleCard from '../components/SaleCard';
import LoadingState from '../components/LoadingState';
import EmptyState from '../components/EmptyState';

export default function SalesScreen({ navigation }) {
  const [sales, setSales] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);

  const fetchSales = useCallback(async (pageNum = 1, isRefresh = false) => {
    try {
      const params = { page: pageNum, per_page: 20 };
      if (search) params.search = search;

      const result = await api.getSales(params);
      const newData = result.data || [];

      if (isRefresh || pageNum === 1) {
        setSales(newData);
      } else {
        setSales(prev => [...prev, ...newData]);
      }

      setHasMore(result.current_page < result.last_page);
      setPage(pageNum);
    } catch (error) {
      console.error('Satışlar yüklenemedi:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
      setLoadingMore(false);
    }
  }, [search]);

  useFocusEffect(
    useCallback(() => {
      setLoading(true);
      fetchSales(1, true);
    }, [fetchSales])
  );

  useEffect(() => {
    const timer = setTimeout(() => {
      setLoading(true);
      fetchSales(1, true);
    }, 500);
    return () => clearTimeout(timer);
  }, [search]);

  const onRefresh = () => {
    setRefreshing(true);
    Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Light);
    fetchSales(1, true);
  };

  const onEndReached = () => {
    if (!loadingMore && hasMore) {
      setLoadingMore(true);
      fetchSales(page + 1);
    }
  };

  if (loading && sales.length === 0) return <LoadingState message="Satışlar yükleniyor..." />;

  return (
    <View style={styles.container}>
      <SearchBar
        value={search}
        onChangeText={setSearch}
        placeholder="Fiş no veya müşteri ara..."
      />
      <FlatList
        data={sales}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item }) => (
          <SaleCard
            sale={item}
            onPress={() => navigation.navigate('SaleDetail', { saleId: item.id })}
          />
        )}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={Colors.primary} />
        }
        onEndReached={onEndReached}
        onEndReachedThreshold={0.5}
        ListEmptyComponent={
          <EmptyState
            icon="receipt-outline"
            title="Satış bulunamadı"
            subtitle={search ? `"${search}" için sonuç yok` : 'Henüz satış kaydı yok'}
          />
        }
        contentContainerStyle={sales.length === 0 && styles.emptyContainer}
        showsVerticalScrollIndicator={false}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.bg,
  },
  emptyContainer: {
    flex: 1,
  },
});
