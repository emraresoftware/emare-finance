import React, { useState, useCallback } from 'react';
import { View, Text, ScrollView, StyleSheet, FlatList, RefreshControl, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect } from '@react-navigation/native';
import * as Haptics from 'expo-haptics';
import { Colors, Spacing, BorderRadius, Shadow, FontSize } from '../theme';
import { formatMoney, formatNumber, formatDateTime, stockStatus } from '../utils/formatters';
import api from '../api/client';
import LoadingState from '../components/LoadingState';
import StatCard from '../components/StatCard';
import EmptyState from '../components/EmptyState';

export default function StockScreen() {
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [overview, setOverview] = useState(null);
  const [alerts, setAlerts] = useState([]);
  const [movements, setMovements] = useState([]);
  const [activeTab, setActiveTab] = useState('alerts'); // 'alerts' | 'movements'

  const fetchData = async () => {
    try {
      const [overviewRes, alertsRes, movementsRes] = await Promise.all([
        api.getStockOverview(),
        api.getStockAlerts(),
        api.getStockMovements({ per_page: 30 }),
      ]);
      setOverview(overviewRes);
      setAlerts(alertsRes.alerts || []);
      setMovements(movementsRes.movements?.data || movementsRes.movements || []);
    } catch (error) {
      console.error('Stok verileri yüklenemedi:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useFocusEffect(useCallback(() => { fetchData(); }, []));

  const onRefresh = () => {
    Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Light);
    setRefreshing(true);
    fetchData();
  };

  if (loading) return <LoadingState message="Stok verileri yükleniyor..." />;

  return (
    <ScrollView
      style={styles.container}
      showsVerticalScrollIndicator={false}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[Colors.primary]} />}
    >
      {/* Genel Bakış */}
      {overview && (
        <View style={styles.statsContainer}>
          <View style={styles.statsRow}>
            <StatCard
              icon="cube-outline"
              title="Toplam Ürün"
              value={formatNumber(overview.total_products || 0)}
              color={Colors.primary}
              compact
            />
            <StatCard
              icon="checkmark-circle-outline"
              title="Stokta"
              value={formatNumber(overview.in_stock || 0)}
              color={Colors.success}
              compact
            />
          </View>
          <View style={styles.statsRow}>
            <StatCard
              icon="alert-circle-outline"
              title="Azalan Stok"
              value={formatNumber(overview.low_stock || 0)}
              color={Colors.warning}
              compact
            />
            <StatCard
              icon="close-circle-outline"
              title="Tükenen"
              value={formatNumber(overview.out_of_stock || 0)}
              color={Colors.danger}
              compact
            />
          </View>
          <View style={[styles.card, Shadow.md]}>
            <View style={styles.totalValueRow}>
              <Ionicons name="wallet-outline" size={24} color={Colors.primary} />
              <View>
                <Text style={styles.totalValueLabel}>Toplam Stok Değeri</Text>
                <Text style={styles.totalValue}>{formatMoney(overview.total_value || 0)}</Text>
              </View>
            </View>
          </View>
        </View>
      )}

      {/* Tab Seçici */}
      <View style={styles.tabRow}>
        <TouchableOpacity
          style={[styles.tab, activeTab === 'alerts' && styles.tabActive]}
          onPress={() => setActiveTab('alerts')}
        >
          <Ionicons
            name="warning-outline"
            size={16}
            color={activeTab === 'alerts' ? Colors.primary : Colors.textSecondary}
          />
          <Text style={[styles.tabText, activeTab === 'alerts' && styles.tabTextActive]}>
            Uyarılar ({alerts.length})
          </Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.tab, activeTab === 'movements' && styles.tabActive]}
          onPress={() => setActiveTab('movements')}
        >
          <Ionicons
            name="swap-horizontal-outline"
            size={16}
            color={activeTab === 'movements' ? Colors.primary : Colors.textSecondary}
          />
          <Text style={[styles.tabText, activeTab === 'movements' && styles.tabTextActive]}>
            Hareketler
          </Text>
        </TouchableOpacity>
      </View>

      {/* Uyarılar */}
      {activeTab === 'alerts' && (
        <View style={styles.listContainer}>
          {alerts.length === 0 ? (
            <EmptyState icon="checkmark-done-outline" title="Harika!" subtitle="Tüm stok seviyeleri normal" />
          ) : (
            alerts.map(item => {
              const status = stockStatus(item.stock_quantity, item.min_stock_level);
              return (
                <View key={item.id} style={[styles.alertCard, Shadow.sm, { borderLeftWidth: 3, borderLeftColor: status.color }]}>
                  <View style={styles.alertInfo}>
                    <Text style={styles.alertName} numberOfLines={1}>{item.name}</Text>
                    <Text style={styles.alertMeta}>
                      {item.barcode && `${item.barcode} • `}
                      {item.category?.name || ''}
                    </Text>
                  </View>
                  <View style={styles.alertStock}>
                    <Text style={[styles.alertStockValue, { color: status.color }]}>
                      {item.stock_quantity}
                    </Text>
                    <Text style={[styles.alertStockLabel, { color: status.color }]}>
                      {status.label}
                    </Text>
                  </View>
                </View>
              );
            })
          )}
        </View>
      )}

      {/* Hareketler */}
      {activeTab === 'movements' && (
        <View style={styles.listContainer}>
          {movements.length === 0 ? (
            <EmptyState icon="swap-horizontal-outline" title="Hareket yok" subtitle="Henüz stok hareketi bulunmuyor" />
          ) : (
            movements.map((movement, index) => (
              <View key={movement.id || index} style={[styles.movementCard, Shadow.sm]}>
                <View style={[
                  styles.movementIcon,
                  { backgroundColor: movement.type === 'in' ? Colors.successLight : Colors.dangerLight }
                ]}>
                  <Ionicons
                    name={movement.type === 'in' ? 'arrow-down' : 'arrow-up'}
                    size={18}
                    color={movement.type === 'in' ? Colors.success : Colors.danger}
                  />
                </View>
                <View style={styles.movementInfo}>
                  <Text style={styles.movementProduct} numberOfLines={1}>
                    {movement.product?.name || movement.product_name || `Ürün #${movement.product_id}`}
                  </Text>
                  <Text style={styles.movementMeta}>
                    {movement.reason || movement.description || ''} • {formatDateTime(movement.created_at)}
                  </Text>
                </View>
                <Text style={[
                  styles.movementQty,
                  { color: movement.type === 'in' ? Colors.success : Colors.danger }
                ]}>
                  {movement.type === 'in' ? '+' : '-'}{movement.quantity}
                </Text>
              </View>
            ))
          )}
        </View>
      )}

      <View style={{ height: 30 }} />
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.bg,
  },
  statsContainer: {
    paddingTop: Spacing.lg,
  },
  statsRow: {
    flexDirection: 'row',
    gap: Spacing.md,
    paddingHorizontal: Spacing.lg,
    marginBottom: Spacing.md,
  },
  card: {
    backgroundColor: Colors.card,
    borderRadius: BorderRadius.lg,
    padding: Spacing.xl,
    marginHorizontal: Spacing.lg,
  },
  totalValueRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: Spacing.lg,
  },
  totalValueLabel: {
    fontSize: FontSize.sm,
    color: Colors.textSecondary,
  },
  totalValue: {
    fontSize: FontSize.xl,
    fontWeight: '800',
    color: Colors.primary,
  },
  tabRow: {
    flexDirection: 'row',
    gap: Spacing.sm,
    paddingHorizontal: Spacing.lg,
    paddingTop: Spacing.xl,
    paddingBottom: Spacing.sm,
  },
  tab: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    paddingVertical: Spacing.sm,
    borderRadius: BorderRadius.full,
    backgroundColor: Colors.card,
    borderWidth: 1,
    borderColor: Colors.border,
  },
  tabActive: {
    backgroundColor: Colors.primaryLight,
    borderColor: Colors.primary,
  },
  tabText: {
    fontSize: FontSize.sm,
    fontWeight: '600',
    color: Colors.textSecondary,
  },
  tabTextActive: {
    color: Colors.primary,
  },
  listContainer: {
    paddingHorizontal: Spacing.lg,
    paddingTop: Spacing.md,
  },
  alertCard: {
    backgroundColor: Colors.card,
    borderRadius: BorderRadius.md,
    padding: Spacing.lg,
    marginBottom: Spacing.sm,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  alertInfo: {
    flex: 1,
    marginRight: Spacing.md,
  },
  alertName: {
    fontSize: FontSize.md,
    fontWeight: '600',
    color: Colors.text,
  },
  alertMeta: {
    fontSize: FontSize.xs,
    color: Colors.textMuted,
    marginTop: 2,
  },
  alertStock: {
    alignItems: 'flex-end',
  },
  alertStockValue: {
    fontSize: FontSize.lg,
    fontWeight: '800',
  },
  alertStockLabel: {
    fontSize: FontSize.xs,
    fontWeight: '600',
  },
  movementCard: {
    backgroundColor: Colors.card,
    borderRadius: BorderRadius.md,
    padding: Spacing.lg,
    marginBottom: Spacing.sm,
    flexDirection: 'row',
    alignItems: 'center',
  },
  movementIcon: {
    width: 36,
    height: 36,
    borderRadius: 18,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: Spacing.md,
  },
  movementInfo: {
    flex: 1,
  },
  movementProduct: {
    fontSize: FontSize.md,
    fontWeight: '600',
    color: Colors.text,
  },
  movementMeta: {
    fontSize: FontSize.xs,
    color: Colors.textMuted,
    marginTop: 2,
  },
  movementQty: {
    fontSize: FontSize.lg,
    fontWeight: '700',
  },
});
