import React, { useState, useCallback } from 'react';
import { View, Text, ScrollView, StyleSheet, Dimensions, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect } from '@react-navigation/native';
import { LineChart, BarChart, PieChart } from 'react-native-chart-kit';
import { Colors, Spacing, BorderRadius, Shadow, FontSize } from '../theme';
import { formatMoney, formatMoneyShort, formatNumber } from '../utils/formatters';
import api from '../api/client';
import LoadingState from '../components/LoadingState';
import StatCard from '../components/StatCard';

const screenWidth = Dimensions.get('window').width;

export default function ReportsScreen() {
  const [loading, setLoading] = useState(true);
  const [daily, setDaily] = useState(null);
  const [topProducts, setTopProducts] = useState([]);
  const [revenueChart, setRevenueChart] = useState(null);
  const [paymentMethods, setPaymentMethods] = useState([]);
  const [period, setPeriod] = useState(7);

  const fetchReports = async () => {
    setLoading(true);
    try {
      const [dailyRes, topRes, revenueRes, paymentRes] = await Promise.all([
        api.getDailyReport(),
        api.getTopProducts({ days: period }),
        api.getRevenueChart({ days: period }),
        api.getPaymentMethods({ days: period }),
      ]);
      setDaily(dailyRes);
      setTopProducts(topRes.products || []);
      setRevenueChart(revenueRes);
      setPaymentMethods(paymentRes.methods || []);
    } catch (error) {
      console.error('Raporlar yüklenemedi:', error);
    } finally {
      setLoading(false);
    }
  };

  useFocusEffect(useCallback(() => { fetchReports(); }, [period]));

  if (loading) return <LoadingState message="Raporlar hazırlanıyor..." />;

  // Gelir Grafiği Verisi
  const chartData = revenueChart?.chart ? {
    labels: revenueChart.chart.map(d => {
      const parts = d.date.split('-');
      return `${parts[2]}/${parts[1]}`;
    }),
    datasets: [{
      data: revenueChart.chart.map(d => parseFloat(d.revenue) || 0),
      color: (opacity = 1) => `rgba(79, 70, 229, ${opacity})`,
      strokeWidth: 2,
    }],
  } : null;

  // Ödeme Yöntemi Pasta Grafiği
  const pieColors = ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4'];
  const pieData = paymentMethods.map((m, i) => ({
    name: m.label || m.method,
    amount: parseFloat(m.total) || 0,
    color: pieColors[i % pieColors.length],
    legendFontColor: Colors.text,
    legendFontSize: 12,
  }));

  const chartConfig = {
    backgroundColor: Colors.card,
    backgroundGradientFrom: Colors.card,
    backgroundGradientTo: Colors.card,
    decimalPlaces: 0,
    color: (opacity = 1) => `rgba(79, 70, 229, ${opacity})`,
    labelColor: () => Colors.textSecondary,
    style: { borderRadius: BorderRadius.lg },
    propsForDots: { r: '4', strokeWidth: '2', stroke: Colors.primary },
  };

  return (
    <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
      {/* Dönem Seçici */}
      <View style={styles.periodRow}>
        {[7, 15, 30].map(d => (
          <TouchableOpacity
            key={d}
            style={[styles.periodBtn, period === d && styles.periodBtnActive]}
            onPress={() => setPeriod(d)}
          >
            <Text style={[styles.periodText, period === d && styles.periodTextActive]}>
              {d} Gün
            </Text>
          </TouchableOpacity>
        ))}
      </View>

      {/* Bugünün Özeti */}
      {daily && (
        <View style={[styles.card, Shadow.md]}>
          <Text style={styles.sectionTitle}>
            <Ionicons name="today-outline" size={18} color={Colors.primary} /> Bugünün Özeti
          </Text>
          <View style={styles.statsGrid}>
            <StatCard icon="receipt-outline" title="Satış" value={formatNumber(daily.sales_count || 0)} compact />
            <StatCard icon="cash-outline" title="Ciro" value={formatMoneyShort(daily.revenue || 0)} compact />
          </View>
        </View>
      )}

      {/* Gelir Grafiği */}
      {chartData && chartData.datasets[0].data.length > 0 && (
        <View style={[styles.card, Shadow.md]}>
          <Text style={styles.sectionTitle}>
            <Ionicons name="trending-up-outline" size={18} color={Colors.primary} /> Gelir Trendi
          </Text>
          <LineChart
            data={chartData}
            width={screenWidth - Spacing.lg * 4}
            height={200}
            chartConfig={chartConfig}
            bezier
            style={styles.chart}
            formatYLabel={(value) => formatMoneyShort(value)}
          />
        </View>
      )}

      {/* En Çok Satan Ürünler */}
      {topProducts.length > 0 && (
        <View style={[styles.card, Shadow.md]}>
          <Text style={styles.sectionTitle}>
            <Ionicons name="trophy-outline" size={18} color={Colors.warning} /> En Çok Satan Ürünler
          </Text>
          {topProducts.slice(0, 10).map((product, index) => (
            <View key={product.id || index} style={styles.topProductRow}>
              <View style={[styles.rankBadge, index < 3 && { backgroundColor: Colors.warningLight }]}>
                <Text style={[styles.rankText, index < 3 && { color: Colors.warning }]}>
                  {index + 1}
                </Text>
              </View>
              <View style={styles.topProductInfo}>
                <Text style={styles.topProductName} numberOfLines={1}>{product.name}</Text>
                <Text style={styles.topProductMeta}>
                  {formatNumber(product.total_quantity)} adet satıldı
                </Text>
              </View>
              <Text style={styles.topProductRevenue}>
                {formatMoney(product.total_revenue)}
              </Text>
            </View>
          ))}
        </View>
      )}

      {/* Ödeme Yöntemleri */}
      {pieData.length > 0 && (
        <View style={[styles.card, Shadow.md]}>
          <Text style={styles.sectionTitle}>
            <Ionicons name="pie-chart-outline" size={18} color={Colors.info} /> Ödeme Dağılımı
          </Text>
          <PieChart
            data={pieData}
            width={screenWidth - Spacing.lg * 4}
            height={200}
            chartConfig={chartConfig}
            accessor="amount"
            backgroundColor="transparent"
            paddingLeft="0"
            absolute={false}
          />
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
  periodRow: {
    flexDirection: 'row',
    gap: Spacing.sm,
    paddingHorizontal: Spacing.lg,
    paddingTop: Spacing.lg,
  },
  periodBtn: {
    flex: 1,
    paddingVertical: Spacing.sm,
    borderRadius: BorderRadius.full,
    backgroundColor: Colors.card,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: Colors.border,
  },
  periodBtnActive: {
    backgroundColor: Colors.primary,
    borderColor: Colors.primary,
  },
  periodText: {
    fontSize: FontSize.sm,
    fontWeight: '600',
    color: Colors.textSecondary,
  },
  periodTextActive: {
    color: '#FFFFFF',
  },
  card: {
    backgroundColor: Colors.card,
    borderRadius: BorderRadius.lg,
    padding: Spacing.xl,
    marginHorizontal: Spacing.lg,
    marginTop: Spacing.lg,
  },
  sectionTitle: {
    fontSize: FontSize.lg,
    fontWeight: '700',
    color: Colors.text,
    marginBottom: Spacing.md,
  },
  statsGrid: {
    flexDirection: 'row',
    gap: Spacing.md,
  },
  chart: {
    borderRadius: BorderRadius.md,
    marginLeft: -Spacing.md,
  },
  topProductRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: Spacing.sm,
    borderBottomWidth: 1,
    borderBottomColor: Colors.borderLight,
  },
  rankBadge: {
    width: 28,
    height: 28,
    borderRadius: 14,
    backgroundColor: Colors.bg,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: Spacing.md,
  },
  rankText: {
    fontSize: FontSize.sm,
    fontWeight: '700',
    color: Colors.textSecondary,
  },
  topProductInfo: {
    flex: 1,
  },
  topProductName: {
    fontSize: FontSize.md,
    fontWeight: '600',
    color: Colors.text,
  },
  topProductMeta: {
    fontSize: FontSize.xs,
    color: Colors.textMuted,
    marginTop: 2,
  },
  topProductRevenue: {
    fontSize: FontSize.md,
    fontWeight: '700',
    color: Colors.success,
  },
});
