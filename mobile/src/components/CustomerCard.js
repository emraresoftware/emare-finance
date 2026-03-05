import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors, Spacing, BorderRadius, Shadow, FontSize } from '../theme';
import { formatMoney } from '../utils/formatters';

export default function CustomerCard({ customer, onPress }) {
  const isDebt = parseFloat(customer.balance) < 0;

  return (
    <TouchableOpacity style={[styles.card, Shadow.sm]} onPress={onPress} activeOpacity={0.7}>
      <View style={styles.left}>
        <View style={[styles.avatar, { backgroundColor: getAvatarColor(customer.name) }]}>
          <Text style={styles.avatarText}>{getInitials(customer.name)}</Text>
        </View>
        <View style={styles.info}>
          <Text style={styles.name} numberOfLines={1}>{customer.name}</Text>
          {customer.phone && <Text style={styles.phone}>{customer.phone}</Text>}
          {customer.sales_count !== undefined && (
            <Text style={styles.salesCount}>{customer.sales_count} satış</Text>
          )}
        </View>
      </View>
      <View style={styles.right}>
        <Text style={[styles.balance, { color: isDebt ? Colors.danger : Colors.success }]}>
          {formatMoney(Math.abs(customer.balance))}
        </Text>
        <Text style={[styles.balanceLabel, { color: isDebt ? Colors.danger : Colors.success }]}>
          {isDebt ? 'Borç' : 'Alacak'}
        </Text>
      </View>
    </TouchableOpacity>
  );
}

function getInitials(name) {
  if (!name) return '?';
  const parts = name.split(' ').filter(Boolean);
  if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase();
  return name.substring(0, 2).toUpperCase();
}

function getAvatarColor(name) {
  const colors = ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#F97316'];
  let hash = 0;
  for (let i = 0; i < (name || '').length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
  return colors[Math.abs(hash) % colors.length];
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
  avatar: {
    width: 44,
    height: 44,
    borderRadius: 22,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: Spacing.md,
  },
  avatarText: {
    fontSize: FontSize.md,
    fontWeight: '700',
    color: Colors.white,
  },
  info: {
    flex: 1,
  },
  name: {
    fontSize: FontSize.md,
    fontWeight: '600',
    color: Colors.text,
  },
  phone: {
    fontSize: FontSize.sm,
    color: Colors.textMuted,
    marginTop: 1,
  },
  salesCount: {
    fontSize: FontSize.xs,
    color: Colors.textSecondary,
    marginTop: 2,
  },
  right: {
    alignItems: 'flex-end',
    marginLeft: Spacing.sm,
  },
  balance: {
    fontSize: FontSize.lg,
    fontWeight: '700',
  },
  balanceLabel: {
    fontSize: FontSize.xs,
    fontWeight: '500',
    marginTop: 2,
  },
});
