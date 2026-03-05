import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors, Spacing, BorderRadius, Shadow, FontSize } from '../theme';
import { formatMoney, formatDateTime, paymentMethodLabel, paymentMethodColor } from '../utils/formatters';

export default function SaleCard({ sale, onPress }) {
  const methodColor = paymentMethodColor(sale.payment_method);

  return (
    <TouchableOpacity style={[styles.card, Shadow.sm]} onPress={onPress} activeOpacity={0.7}>
      <View style={styles.left}>
        <View style={[styles.icon, { backgroundColor: methodColor + '15' }]}>
          <Ionicons
            name={sale.payment_method === 'cash' ? 'cash-outline' : sale.payment_method === 'card' ? 'card-outline' : 'swap-horizontal-outline'}
            size={20}
            color={methodColor}
          />
        </View>
        <View style={styles.info}>
          <Text style={styles.receiptNo} numberOfLines={1}>
            {sale.receipt_no || `#${sale.id}`}
          </Text>
          <Text style={styles.customer} numberOfLines={1}>
            {sale.customer?.name || 'Genel Müşteri'}
          </Text>
          <Text style={styles.date}>{formatDateTime(sale.sold_at)}</Text>
        </View>
      </View>
      <View style={styles.right}>
        <Text style={styles.amount}>{formatMoney(sale.grand_total)}</Text>
        <View style={[styles.methodBadge, { backgroundColor: methodColor + '15' }]}>
          <Text style={[styles.methodText, { color: methodColor }]}>
            {paymentMethodLabel(sale.payment_method)}
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
  receiptNo: {
    fontSize: FontSize.md,
    fontWeight: '600',
    color: Colors.text,
  },
  customer: {
    fontSize: FontSize.sm,
    color: Colors.textSecondary,
    marginTop: 1,
  },
  date: {
    fontSize: FontSize.xs,
    color: Colors.textMuted,
    marginTop: 2,
  },
  right: {
    alignItems: 'flex-end',
    marginLeft: Spacing.sm,
  },
  amount: {
    fontSize: FontSize.lg,
    fontWeight: '700',
    color: Colors.text,
  },
  methodBadge: {
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: BorderRadius.full,
    marginTop: 4,
  },
  methodText: {
    fontSize: FontSize.xs,
    fontWeight: '600',
  },
});
