import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors, Spacing, BorderRadius, Shadow, FontSize } from '../theme';
import { formatMoney, stockStatus } from '../utils/formatters';

export default function ProductCard({ product, onPress }) {
  const status = stockStatus(product.stock_quantity, product.critical_stock);

  return (
    <TouchableOpacity style={[styles.card, Shadow.sm]} onPress={onPress} activeOpacity={0.7}>
      <View style={styles.left}>
        <View style={[styles.icon, { backgroundColor: Colors.primaryLight }]}>
          <Ionicons name="cube-outline" size={22} color={Colors.primary} />
        </View>
        <View style={styles.info}>
          <Text style={styles.name} numberOfLines={1}>{product.name}</Text>
          <Text style={styles.barcode}>{product.barcode || '-'}</Text>
          {product.category && (
            <Text style={styles.category}>{product.category.name}</Text>
          )}
        </View>
      </View>
      <View style={styles.right}>
        <Text style={styles.price}>{formatMoney(product.sale_price)}</Text>
        <View style={[styles.stockBadge, { backgroundColor: status.bg }]}>
          <Text style={[styles.stockText, { color: status.color }]}>
            {product.stock_quantity} {product.unit || 'Ad.'}
          </Text>
        </View>
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  card: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: Colors.card,
    borderRadius: BorderRadius.lg,
    padding: Spacing.lg,
    marginHorizontal: Spacing.lg,
    marginBottom: Spacing.sm,
  },
  left: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  icon: {
    width: 44,
    height: 44,
    borderRadius: BorderRadius.md,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: Spacing.md,
  },
  info: {
    flex: 1,
  },
  name: {
    fontSize: FontSize.md,
    fontWeight: '600',
    color: Colors.text,
  },
  barcode: {
    fontSize: FontSize.sm,
    color: Colors.textMuted,
    marginTop: 1,
  },
  category: {
    fontSize: FontSize.xs,
    color: Colors.primary,
    marginTop: 2,
  },
  right: {
    alignItems: 'flex-end',
    marginLeft: Spacing.sm,
  },
  price: {
    fontSize: FontSize.lg,
    fontWeight: '700',
    color: Colors.text,
  },
  stockBadge: {
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: BorderRadius.full,
    marginTop: 4,
  },
  stockText: {
    fontSize: FontSize.xs,
    fontWeight: '600',
  },
});
