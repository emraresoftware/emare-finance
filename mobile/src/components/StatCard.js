import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors, Spacing, BorderRadius, Shadow, FontSize } from '../theme';

export default function StatCard({ title, value, subtitle, icon, iconColor, color, bgColor, onPress, trend, compact }) {
  const Wrapper = onPress ? TouchableOpacity : View;
  const resolvedColor = color || iconColor || Colors.primary;

  return (
    <Wrapper style={[styles.card, Shadow.md, compact && styles.cardCompact]} onPress={onPress} activeOpacity={0.7}>
      <View style={styles.row}>
        <View style={[styles.iconBox, compact && styles.iconBoxCompact, { backgroundColor: bgColor || `${resolvedColor}15` }]}>
          <Ionicons name={icon || 'stats-chart'} size={compact ? 16 : 20} color={resolvedColor} />
        </View>
        {trend !== undefined && (
          <View style={[styles.trendBadge, { backgroundColor: trend >= 0 ? Colors.successLight : Colors.dangerLight }]}>
            <Ionicons
              name={trend >= 0 ? 'trending-up' : 'trending-down'}
              size={12}
              color={trend >= 0 ? Colors.success : Colors.danger}
            />
            <Text style={[styles.trendText, { color: trend >= 0 ? Colors.success : Colors.danger }]}>
              %{Math.abs(trend).toFixed(0)}
            </Text>
          </View>
        )}
      </View>
      <Text style={[styles.value, compact && styles.valueCompact]} numberOfLines={1}>{value}</Text>
      <Text style={styles.title} numberOfLines={1}>{title}</Text>
      {subtitle && <Text style={styles.subtitle} numberOfLines={1}>{subtitle}</Text>}
    </Wrapper>
  );
}

const styles = StyleSheet.create({
  card: {
    flex: 1,
    backgroundColor: Colors.card,
    borderRadius: BorderRadius.lg,
    padding: Spacing.lg,
    marginHorizontal: Spacing.xs,
  },
  cardCompact: {
    padding: Spacing.md,
  },
  row: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: Spacing.md,
  },
  iconBox: {
    width: 40,
    height: 40,
    borderRadius: BorderRadius.md,
    justifyContent: 'center',
    alignItems: 'center',
  },
  iconBoxCompact: {
    width: 32,
    height: 32,
    borderRadius: 10,
  },
  value: {
    fontSize: FontSize.xxl,
    fontWeight: '700',
    color: Colors.text,
    marginBottom: 2,
  },
  valueCompact: {
    fontSize: FontSize.lg,
  },
  title: {
    fontSize: FontSize.sm,
    color: Colors.textSecondary,
    fontWeight: '500',
  },
  subtitle: {
    fontSize: FontSize.xs,
    color: Colors.textMuted,
    marginTop: 2,
  },
  trendBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: BorderRadius.full,
    gap: 2,
  },
  trendText: {
    fontSize: FontSize.xs,
    fontWeight: '600',
  },
});
