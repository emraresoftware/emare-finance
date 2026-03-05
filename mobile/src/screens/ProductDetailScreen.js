import React, { useState, useEffect } from 'react';
import { View, Text, ScrollView, StyleSheet, Dimensions } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors, Spacing, BorderRadius, Shadow, FontSize } from '../theme';
import { formatMoney, stockStatus, formatNumber } from '../utils/formatters';
import api from '../api/client';
import LoadingState from '../components/LoadingState';
import StatCard from '../components/StatCard';

const screenWidth = Dimensions.get('window').width;

export default function ProductDetailScreen({ route }) {
  const { productId } = route.params;
  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);

  const fetchProduct = async () => {
    try {
      const result = await api.getProduct(productId);
      setProduct(result.product);
    } catch (error) {
      console.error('Ürün detayı yüklenemedi:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchProduct(); }, [productId]);

  if (loading) return <LoadingState message="Yükleniyor..." />;
  if (!product) return <LoadingState message="Ürün bulunamadı" />;

  const stock = stockStatus(product.stock_quantity, product.min_stock_level);

  return (
    <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
      {/* Ürün Başlık */}
      <View style={[styles.card, Shadow.md]}>
        <View style={styles.iconContainer}>
          <Ionicons name="cube" size={40} color={Colors.primary} />
        </View>
        <Text style={styles.productName}>{product.name}</Text>
        {product.barcode && (
          <View style={styles.barcodeRow}>
            <Ionicons name="barcode-outline" size={16} color={Colors.textSecondary} />
            <Text style={styles.barcode}>{product.barcode}</Text>
          </View>
        )}
        {product.category && (
          <View style={styles.categoryBadge}>
            <Ionicons name="folder-outline" size={14} color={Colors.primary} />
            <Text style={styles.categoryText}>{product.category.name}</Text>
          </View>
        )}
      </View>

      {/* Fiyat & Stok */}
      <View style={styles.gridRow}>
        <View style={[styles.miniCard, Shadow.sm]}>
          <Text style={styles.miniLabel}>Satış Fiyatı</Text>
          <Text style={styles.miniValue}>{formatMoney(product.sale_price)}</Text>
        </View>
        <View style={[styles.miniCard, Shadow.sm]}>
          <Text style={styles.miniLabel}>Alış Fiyatı</Text>
          <Text style={styles.miniValue}>{formatMoney(product.purchase_price)}</Text>
        </View>
      </View>

      <View style={styles.gridRow}>
        <View style={[styles.miniCard, Shadow.sm, { borderLeftWidth: 3, borderLeftColor: stock.color }]}>
          <Text style={styles.miniLabel}>Stok Miktarı</Text>
          <Text style={[styles.miniValue, { color: stock.color }]}>{product.stock_quantity}</Text>
          <Text style={[styles.miniStatus, { color: stock.color }]}>{stock.label}</Text>
        </View>
        <View style={[styles.miniCard, Shadow.sm]}>
          <Text style={styles.miniLabel}>Min. Stok</Text>
          <Text style={styles.miniValue}>{product.min_stock_level || 0}</Text>
        </View>
      </View>

      {/* Kâr Marjı */}
      {product.sale_price && product.purchase_price && (
        <View style={[styles.card, Shadow.md]}>
          <Text style={styles.sectionTitle}>Kâr Bilgisi</Text>
          <View style={styles.profitRow}>
            <View style={styles.profitItem}>
              <Text style={styles.profitLabel}>Birim Kâr</Text>
              <Text style={[styles.profitValue, { color: Colors.success }]}>
                {formatMoney(product.sale_price - product.purchase_price)}
              </Text>
            </View>
            <View style={styles.profitItem}>
              <Text style={styles.profitLabel}>Kâr Marjı</Text>
              <Text style={[styles.profitValue, { color: Colors.success }]}>
                %{((product.sale_price - product.purchase_price) / product.sale_price * 100).toFixed(1)}
              </Text>
            </View>
            <View style={styles.profitItem}>
              <Text style={styles.profitLabel}>Stok Değeri</Text>
              <Text style={styles.profitValue}>
                {formatMoney(product.stock_quantity * product.sale_price)}
              </Text>
            </View>
          </View>
        </View>
      )}

      {/* Satış İstatistikleri */}
      {product.sales_stats && (
        <View style={[styles.card, Shadow.md]}>
          <Text style={styles.sectionTitle}>Son 30 Gün Satış</Text>
          <View style={styles.statsGrid}>
            <StatCard
              icon="cart-outline"
              title="Toplam Satış"
              value={formatNumber(product.sales_stats.total_sold)}
              compact
            />
            <StatCard
              icon="cash-outline"
              title="Toplam Gelir"
              value={formatMoney(product.sales_stats.total_revenue)}
              compact
            />
          </View>
        </View>
      )}

      {/* Ek Bilgiler */}
      <View style={[styles.card, Shadow.md]}>
        <Text style={styles.sectionTitle}>Detaylar</Text>
        {product.unit && (
          <InfoRow icon="resize-outline" label="Birim" value={product.unit} />
        )}
        {product.vat_rate !== undefined && (
          <InfoRow icon="receipt-outline" label="KDV Oranı" value={`%${product.vat_rate}`} />
        )}
        {product.brand && (
          <InfoRow icon="pricetag-outline" label="Marka" value={product.brand} />
        )}
        <InfoRow
          icon={product.is_active ? 'checkmark-circle' : 'close-circle'}
          label="Durum"
          value={product.is_active ? 'Aktif' : 'Pasif'}
          valueColor={product.is_active ? Colors.success : Colors.danger}
        />
      </View>

      <View style={{ height: 30 }} />
    </ScrollView>
  );
}

function InfoRow({ icon, label, value, valueColor }) {
  return (
    <View style={infoStyles.row}>
      <View style={infoStyles.left}>
        <Ionicons name={icon} size={16} color={Colors.textSecondary} />
        <Text style={infoStyles.label}>{label}</Text>
      </View>
      <Text style={[infoStyles.value, valueColor && { color: valueColor }]}>{value}</Text>
    </View>
  );
}

const infoStyles = StyleSheet.create({
  row: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: Spacing.sm,
    borderBottomWidth: 1,
    borderBottomColor: Colors.borderLight,
  },
  left: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: Spacing.sm,
  },
  label: {
    fontSize: FontSize.md,
    color: Colors.textSecondary,
  },
  value: {
    fontSize: FontSize.md,
    fontWeight: '600',
    color: Colors.text,
  },
});

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
  iconContainer: {
    width: 72,
    height: 72,
    borderRadius: 36,
    backgroundColor: Colors.primaryLight,
    justifyContent: 'center',
    alignItems: 'center',
    alignSelf: 'center',
    marginBottom: Spacing.md,
  },
  productName: {
    fontSize: FontSize.title,
    fontWeight: '800',
    color: Colors.text,
    textAlign: 'center',
  },
  barcodeRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    marginTop: Spacing.sm,
  },
  barcode: {
    fontSize: FontSize.sm,
    color: Colors.textSecondary,
    fontFamily: 'monospace',
  },
  categoryBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'center',
    gap: 4,
    backgroundColor: Colors.primaryLight,
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: BorderRadius.full,
    marginTop: Spacing.md,
  },
  categoryText: {
    fontSize: FontSize.sm,
    fontWeight: '600',
    color: Colors.primary,
  },
  gridRow: {
    flexDirection: 'row',
    gap: Spacing.md,
    marginHorizontal: Spacing.lg,
    marginTop: Spacing.md,
  },
  miniCard: {
    flex: 1,
    backgroundColor: Colors.card,
    borderRadius: BorderRadius.md,
    padding: Spacing.lg,
  },
  miniLabel: {
    fontSize: FontSize.xs,
    color: Colors.textMuted,
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  miniValue: {
    fontSize: FontSize.xl,
    fontWeight: '700',
    color: Colors.text,
    marginTop: 4,
  },
  miniStatus: {
    fontSize: FontSize.xs,
    fontWeight: '600',
    marginTop: 2,
  },
  sectionTitle: {
    fontSize: FontSize.lg,
    fontWeight: '700',
    color: Colors.text,
    marginBottom: Spacing.md,
  },
  profitRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  profitItem: {
    alignItems: 'center',
  },
  profitLabel: {
    fontSize: FontSize.xs,
    color: Colors.textMuted,
    marginBottom: 4,
  },
  profitValue: {
    fontSize: FontSize.md,
    fontWeight: '700',
    color: Colors.text,
  },
  statsGrid: {
    flexDirection: 'row',
    gap: Spacing.md,
  },
});
