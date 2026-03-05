import React, { useState, useEffect } from 'react';
import { View, Text, ScrollView, StyleSheet, RefreshControl } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors, Spacing, BorderRadius, Shadow, FontSize } from '../theme';
import { formatMoney, formatDateTime, paymentMethodLabel, paymentMethodColor } from '../utils/formatters';
import api from '../api/client';
import LoadingState from '../components/LoadingState';

export default function SaleDetailScreen({ route }) {
  const { saleId } = route.params;
  const [sale, setSale] = useState(null);
  const [loading, setLoading] = useState(true);

  const fetchSale = async () => {
    try {
      const result = await api.getSale(saleId);
      setSale(result.sale);
    } catch (error) {
      console.error('Satış detayı yüklenemedi:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchSale(); }, [saleId]);

  if (loading) return <LoadingState message="Yükleniyor..." />;
  if (!sale) return <LoadingState message="Satış bulunamadı" />;

  const methodColor = paymentMethodColor(sale.payment_method);

  return (
    <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
      {/* Başlık Kartı */}
      <View style={[styles.card, Shadow.md]}>
        <View style={styles.headerRow}>
          <View>
            <Text style={styles.receiptNo}>{sale.receipt_no || `#${sale.id}`}</Text>
            <Text style={styles.date}>{formatDateTime(sale.sold_at)}</Text>
          </View>
          <View style={[styles.statusBadge, { backgroundColor: sale.status === 'completed' ? Colors.successLight : Colors.dangerLight }]}>
            <Text style={[styles.statusText, { color: sale.status === 'completed' ? Colors.success : Colors.danger }]}>
              {sale.status === 'completed' ? 'Tamamlandı' : sale.status === 'cancelled' ? 'İptal' : 'İade'}
            </Text>
          </View>
        </View>

        <View style={styles.divider} />

        {/* Müşteri & Şube */}
        <View style={styles.infoRow}>
          <Ionicons name="person-outline" size={16} color={Colors.textSecondary} />
          <Text style={styles.infoText}>{sale.customer?.name || 'Genel Müşteri'}</Text>
        </View>
        {sale.branch && (
          <View style={styles.infoRow}>
            <Ionicons name="storefront-outline" size={16} color={Colors.textSecondary} />
            <Text style={styles.infoText}>{sale.branch.name}</Text>
          </View>
        )}
        <View style={styles.infoRow}>
          <Ionicons name="card-outline" size={16} color={methodColor} />
          <Text style={[styles.infoText, { color: methodColor }]}>{paymentMethodLabel(sale.payment_method)}</Text>
        </View>
      </View>

      {/* Ürünler */}
      <View style={[styles.card, Shadow.md]}>
        <Text style={styles.sectionTitle}>Ürünler ({sale.items?.length || 0})</Text>
        {sale.items?.map((item, index) => (
          <View key={item.id || index}>
            {index > 0 && <View style={styles.itemDivider} />}
            <View style={styles.itemRow}>
              <View style={styles.itemInfo}>
                <Text style={styles.itemName} numberOfLines={2}>{item.product_name || item.product?.name}</Text>
                <Text style={styles.itemMeta}>
                  {item.quantity} × {formatMoney(item.unit_price)}
                </Text>
              </View>
              <Text style={styles.itemTotal}>{formatMoney(item.total)}</Text>
            </View>
          </View>
        ))}
      </View>

      {/* Toplam */}
      <View style={[styles.card, Shadow.md]}>
        <Text style={styles.sectionTitle}>Özet</Text>
        <View style={styles.summaryRow}>
          <Text style={styles.summaryLabel}>Ara Toplam</Text>
          <Text style={styles.summaryValue}>{formatMoney(sale.subtotal || sale.grand_total)}</Text>
        </View>
        {parseFloat(sale.discount) > 0 && (
          <View style={styles.summaryRow}>
            <Text style={styles.summaryLabel}>İndirim</Text>
            <Text style={[styles.summaryValue, { color: Colors.danger }]}>-{formatMoney(sale.discount)}</Text>
          </View>
        )}
        {parseFloat(sale.vat_total) > 0 && (
          <View style={styles.summaryRow}>
            <Text style={styles.summaryLabel}>KDV</Text>
            <Text style={styles.summaryValue}>{formatMoney(sale.vat_total)}</Text>
          </View>
        )}
        <View style={styles.totalDivider} />
        <View style={styles.summaryRow}>
          <Text style={styles.totalLabel}>Genel Toplam</Text>
          <Text style={styles.totalValue}>{formatMoney(sale.grand_total)}</Text>
        </View>
      </View>

      <View style={{ height: 30 }} />
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.bg,
  },
  card: {
    backgroundColor: Colors.card,
    borderRadius: BorderRadius.lg,
    padding: Spacing.xl,
    marginHorizontal: Spacing.lg,
    marginTop: Spacing.lg,
  },
  headerRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
  },
  receiptNo: {
    fontSize: FontSize.xl,
    fontWeight: '700',
    color: Colors.text,
  },
  date: {
    fontSize: FontSize.sm,
    color: Colors.textSecondary,
    marginTop: 4,
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: BorderRadius.full,
  },
  statusText: {
    fontSize: FontSize.sm,
    fontWeight: '600',
  },
  divider: {
    height: 1,
    backgroundColor: Colors.border,
    marginVertical: Spacing.lg,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: Spacing.sm,
    gap: Spacing.sm,
  },
  infoText: {
    fontSize: FontSize.md,
    color: Colors.textSecondary,
  },
  sectionTitle: {
    fontSize: FontSize.lg,
    fontWeight: '700',
    color: Colors.text,
    marginBottom: Spacing.lg,
  },
  itemRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: Spacing.sm,
  },
  itemInfo: {
    flex: 1,
    marginRight: Spacing.md,
  },
  itemName: {
    fontSize: FontSize.md,
    fontWeight: '500',
    color: Colors.text,
  },
  itemMeta: {
    fontSize: FontSize.sm,
    color: Colors.textMuted,
    marginTop: 2,
  },
  itemTotal: {
    fontSize: FontSize.md,
    fontWeight: '600',
    color: Colors.text,
  },
  itemDivider: {
    height: 1,
    backgroundColor: Colors.borderLight,
  },
  summaryRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: Spacing.sm,
  },
  summaryLabel: {
    fontSize: FontSize.md,
    color: Colors.textSecondary,
  },
  summaryValue: {
    fontSize: FontSize.md,
    color: Colors.text,
    fontWeight: '500',
  },
  totalDivider: {
    height: 1,
    backgroundColor: Colors.border,
    marginVertical: Spacing.md,
  },
  totalLabel: {
    fontSize: FontSize.lg,
    fontWeight: '700',
    color: Colors.text,
  },
  totalValue: {
    fontSize: FontSize.xl,
    fontWeight: '800',
    color: Colors.primary,
  },
});
