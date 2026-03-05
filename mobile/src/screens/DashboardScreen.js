import React, { useState, useCallback } from 'react';
import { View, Text, ScrollView, StyleSheet, RefreshControl, TouchableOpacity, Dimensions } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { useFocusEffect } from '@react-navigation/native';
import * as Haptics from 'expo-haptics';
import { Colors, Spacing, BorderRadius, Shadow, FontSize } from '../theme';
import { formatMoney, formatMoneyShort, formatDateTime, timeAgo } from '../utils/formatters';
import api from '../api/client';
import StatCard from '../components/StatCard';
import SaleCard from '../components/SaleCard';
import SectionHeader from '../components/SectionHeader';
import LoadingState from '../components/LoadingState';

const { width } = Dimensions.get('window');

export default function DashboardScreen({ navigation }) {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchData = useCallback(async () => {
    try {
      const result = await api.getDashboard();
      setData(result);
    } catch (error) {
      console.error('Dashboard yüklenemedi:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, []);

  useFocusEffect(
    useCallback(() => {
      fetchData();
    }, [fetchData])
  );

  const onRefresh = () => {
    setRefreshing(true);
    Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Light);
    fetchData();
  };

  if (loading) return <LoadingState message="Veriler yükleniyor..." />;

  const stats = data?.stats || {};

  return (
    <ScrollView
      style={styles.container}
      showsVerticalScrollIndicator={false}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={Colors.primary} />}
    >
      {/* Gelir Kartı */}
      <LinearGradient
        colors={[Colors.primary, Colors.primaryDark]}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
        style={styles.heroCard}
      >
        <View style={styles.heroTop}>
          <View>
            <Text style={styles.heroLabel}>Bugünkü Gelir</Text>
            <Text style={styles.heroAmount}>{formatMoney(stats.today_revenue)}</Text>
          </View>
          <View style={styles.heroBadge}>
            <Ionicons name="today-outline" size={14} color={Colors.white} />
            <Text style={styles.heroBadgeText}>{stats.today_sales_count || 0} satış</Text>
          </View>
        </View>
        <View style={styles.heroDivider} />
        <View style={styles.heroBottom}>
          <View style={styles.heroStat}>
            <Text style={styles.heroStatLabel}>Bu Hafta</Text>
            <Text style={styles.heroStatValue}>{formatMoneyShort(stats.week_revenue)}</Text>
          </View>
          <View style={styles.heroStatDivider} />
          <View style={styles.heroStat}>
            <Text style={styles.heroStatLabel}>Bu Ay</Text>
            <Text style={styles.heroStatValue}>{formatMoneyShort(stats.month_revenue)}</Text>
          </View>
          <View style={styles.heroStatDivider} />
          <View style={styles.heroStat}>
            <Text style={styles.heroStatLabel}>Toplam</Text>
            <Text style={styles.heroStatValue}>{formatMoneyShort(stats.total_revenue)}</Text>
          </View>
        </View>
      </LinearGradient>

      {/* İstatistik Kartları */}
      <View style={styles.statsRow}>
        <StatCard
          title="Ürünler"
          value={stats.total_products || 0}
          icon="cube-outline"
          iconColor={Colors.info}
          bgColor={Colors.infoLight}
          onPress={() => navigation.navigate('ProductsTab')}
        />
        <StatCard
          title="Cariler"
          value={stats.total_customers || 0}
          icon="people-outline"
          iconColor={Colors.success}
          bgColor={Colors.successLight}
          onPress={() => navigation.navigate('CustomersTab')}
        />
      </View>

      <View style={styles.statsRow}>
        <StatCard
          title="Düşük Stok"
          value={stats.low_stock_count || 0}
          icon="warning-outline"
          iconColor={stats.low_stock_count > 0 ? Colors.danger : Colors.success}
          bgColor={stats.low_stock_count > 0 ? Colors.dangerLight : Colors.successLight}
          onPress={() => navigation.navigate('MoreTab', { screen: 'Stock' })}
        />
        <StatCard
          title="Personel"
          value={stats.total_staff || 0}
          icon="person-outline"
          iconColor={Colors.warning}
          bgColor={Colors.warningLight}
        />
      </View>

      {/* Düşük Stok Uyarıları */}
      {data?.low_stock?.length > 0 && (
        <>
          <SectionHeader
            title="Düşük Stok Uyarıları"
            icon="alert-circle-outline"
            actionText="Tümü"
            onAction={() => navigation.navigate('MoreTab', { screen: 'Stock' })}
          />
          {data.low_stock.map((product) => (
            <View key={product.id} style={[styles.alertCard, Shadow.sm]}>
              <View style={styles.alertIcon}>
                <Ionicons name="alert-circle" size={20} color={Colors.danger} />
              </View>
              <View style={styles.alertInfo}>
                <Text style={styles.alertName} numberOfLines={1}>{product.name}</Text>
                <Text style={styles.alertBarcode}>{product.barcode}</Text>
              </View>
              <View style={styles.alertRight}>
                <Text style={styles.alertStock}>{product.stock_quantity} {product.unit}</Text>
                <Text style={styles.alertCritical}>Min: {product.critical_stock}</Text>
              </View>
            </View>
          ))}
        </>
      )}

      {/* Son Satışlar */}
      <SectionHeader
        title="Son Satışlar"
        icon="receipt-outline"
        actionText="Tümü"
        onAction={() => navigation.navigate('SalesTab')}
      />
      {data?.recent_sales?.length > 0 ? (
        data.recent_sales.map((sale) => (
          <SaleCard
            key={sale.id}
            sale={sale}
            onPress={() => navigation.navigate('SalesTab', { screen: 'SaleDetail', params: { saleId: sale.id } })}
          />
        ))
      ) : (
        <View style={styles.emptyMini}>
          <Text style={styles.emptyText}>Henüz satış verisi yok</Text>
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
  heroCard: {
    margin: Spacing.lg,
    borderRadius: BorderRadius.xl,
    padding: Spacing.xl,
  },
  heroTop: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
  },
  heroLabel: {
    fontSize: FontSize.sm,
    color: 'rgba(255,255,255,0.7)',
    fontWeight: '500',
  },
  heroAmount: {
    fontSize: FontSize.title,
    fontWeight: '800',
    color: Colors.white,
    marginTop: 4,
  },
  heroBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255,255,255,0.2)',
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: BorderRadius.full,
    gap: 4,
  },
  heroBadgeText: {
    fontSize: FontSize.xs,
    color: Colors.white,
    fontWeight: '600',
  },
  heroDivider: {
    height: 1,
    backgroundColor: 'rgba(255,255,255,0.15)',
    marginVertical: Spacing.lg,
  },
  heroBottom: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  heroStat: {
    flex: 1,
    alignItems: 'center',
  },
  heroStatLabel: {
    fontSize: FontSize.xs,
    color: 'rgba(255,255,255,0.6)',
    marginBottom: 4,
  },
  heroStatValue: {
    fontSize: FontSize.lg,
    fontWeight: '700',
    color: Colors.white,
  },
  heroStatDivider: {
    width: 1,
    backgroundColor: 'rgba(255,255,255,0.15)',
  },
  statsRow: {
    flexDirection: 'row',
    paddingHorizontal: Spacing.md,
    marginBottom: Spacing.sm,
  },
  alertCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: Colors.card,
    borderRadius: BorderRadius.md,
    padding: Spacing.md,
    marginHorizontal: Spacing.lg,
    marginBottom: Spacing.sm,
    borderLeftWidth: 3,
    borderLeftColor: Colors.danger,
  },
  alertIcon: {
    marginRight: Spacing.md,
  },
  alertInfo: {
    flex: 1,
  },
  alertName: {
    fontSize: FontSize.sm,
    fontWeight: '600',
    color: Colors.text,
  },
  alertBarcode: {
    fontSize: FontSize.xs,
    color: Colors.textMuted,
  },
  alertRight: {
    alignItems: 'flex-end',
  },
  alertStock: {
    fontSize: FontSize.sm,
    fontWeight: '700',
    color: Colors.danger,
  },
  alertCritical: {
    fontSize: FontSize.xs,
    color: Colors.textMuted,
  },
  emptyMini: {
    paddingVertical: Spacing.xxl,
    alignItems: 'center',
  },
  emptyText: {
    fontSize: FontSize.md,
    color: Colors.textMuted,
  },
});
