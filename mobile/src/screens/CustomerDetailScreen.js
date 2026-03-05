import React, { useState, useEffect } from 'react';
import { View, Text, ScrollView, StyleSheet, TouchableOpacity, Linking, FlatList } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors, Spacing, BorderRadius, Shadow, FontSize } from '../theme';
import { formatMoney, formatDate, formatNumber } from '../utils/formatters';
import api from '../api/client';
import LoadingState from '../components/LoadingState';
import SaleCard from '../components/SaleCard';
import StatCard from '../components/StatCard';

export default function CustomerDetailScreen({ route, navigation }) {
  const { customerId } = route.params;
  const [customer, setCustomer] = useState(null);
  const [recentSales, setRecentSales] = useState([]);
  const [loading, setLoading] = useState(true);

  const fetchCustomer = async () => {
    try {
      const [customerResult, salesResult] = await Promise.all([
        api.getCustomer(customerId),
        api.getCustomerSales(customerId, { per_page: 5 }),
      ]);
      setCustomer(customerResult.customer);
      setRecentSales(salesResult.sales?.data || salesResult.sales || []);
    } catch (error) {
      console.error('Müşteri detayı yüklenemedi:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchCustomer(); }, [customerId]);

  if (loading) return <LoadingState message="Yükleniyor..." />;
  if (!customer) return <LoadingState message="Müşteri bulunamadı" />;

  const initials = customer.name
    ? customer.name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase()
    : '?';

  const balance = parseFloat(customer.balance) || 0;

  return (
    <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
      {/* Profil Kartı */}
      <View style={[styles.card, Shadow.md]}>
        <View style={styles.avatarContainer}>
          <View style={styles.avatar}>
            <Text style={styles.avatarText}>{initials}</Text>
          </View>
        </View>
        <Text style={styles.customerName}>{customer.name}</Text>

        {/* İletişim Butonları */}
        <View style={styles.contactRow}>
          {customer.phone && (
            <TouchableOpacity
              style={[styles.contactBtn, { backgroundColor: Colors.successLight }]}
              onPress={() => Linking.openURL(`tel:${customer.phone}`)}
            >
              <Ionicons name="call" size={20} color={Colors.success} />
              <Text style={[styles.contactBtnText, { color: Colors.success }]}>Ara</Text>
            </TouchableOpacity>
          )}
          {customer.phone && (
            <TouchableOpacity
              style={[styles.contactBtn, { backgroundColor: Colors.infoLight }]}
              onPress={() => Linking.openURL(`sms:${customer.phone}`)}
            >
              <Ionicons name="chatbubble" size={20} color={Colors.info} />
              <Text style={[styles.contactBtnText, { color: Colors.info }]}>Mesaj</Text>
            </TouchableOpacity>
          )}
          {customer.email && (
            <TouchableOpacity
              style={[styles.contactBtn, { backgroundColor: Colors.warningLight }]}
              onPress={() => Linking.openURL(`mailto:${customer.email}`)}
            >
              <Ionicons name="mail" size={20} color={Colors.warning} />
              <Text style={[styles.contactBtnText, { color: Colors.warning }]}>E-posta</Text>
            </TouchableOpacity>
          )}
        </View>
      </View>

      {/* Bilgiler */}
      <View style={[styles.card, Shadow.md]}>
        <Text style={styles.sectionTitle}>İletişim Bilgileri</Text>
        {customer.phone && (
          <InfoRow icon="call-outline" label="Telefon" value={customer.phone} />
        )}
        {customer.email && (
          <InfoRow icon="mail-outline" label="E-posta" value={customer.email} />
        )}
        {customer.address && (
          <InfoRow icon="location-outline" label="Adres" value={customer.address} />
        )}
        {customer.tax_number && (
          <InfoRow icon="document-text-outline" label="Vergi No" value={customer.tax_number} />
        )}
        {customer.tax_office && (
          <InfoRow icon="business-outline" label="Vergi Dairesi" value={customer.tax_office} />
        )}
      </View>

      {/* Finansal Bilgiler */}
      <View style={[styles.card, Shadow.md]}>
        <Text style={styles.sectionTitle}>Finansal Özet</Text>
        <View style={styles.statsGrid}>
          <View style={styles.statItem}>
            <Ionicons name="receipt-outline" size={24} color={Colors.primary} />
            <Text style={styles.statValue}>{formatNumber(customer.sales_count || 0)}</Text>
            <Text style={styles.statLabel}>Toplam Satış</Text>
          </View>
          <View style={styles.statItem}>
            <Ionicons name="cash-outline" size={24} color={Colors.success} />
            <Text style={styles.statValue}>{formatMoney(customer.total_purchases || 0)}</Text>
            <Text style={styles.statLabel}>Toplam Alışveriş</Text>
          </View>
          <View style={styles.statItem}>
            <Ionicons
              name={balance >= 0 ? 'trending-up' : 'trending-down'}
              size={24}
              color={balance >= 0 ? Colors.success : Colors.danger}
            />
            <Text style={[styles.statValue, { color: balance >= 0 ? Colors.success : Colors.danger }]}>
              {formatMoney(Math.abs(balance))}
            </Text>
            <Text style={styles.statLabel}>{balance >= 0 ? 'Alacak' : 'Borç'}</Text>
          </View>
        </View>
      </View>

      {/* Son Satışlar */}
      {recentSales.length > 0 && (
        <View style={[styles.card, Shadow.md]}>
          <Text style={styles.sectionTitle}>Son Satışlar</Text>
          {recentSales.map(sale => (
            <SaleCard
              key={sale.id}
              sale={sale}
              onPress={() => navigation.navigate('SaleDetail', { saleId: sale.id })}
            />
          ))}
        </View>
      )}

      <View style={{ height: 30 }} />
    </ScrollView>
  );
}

function InfoRow({ icon, label, value }) {
  return (
    <View style={infoStyles.row}>
      <View style={infoStyles.left}>
        <Ionicons name={icon} size={16} color={Colors.textSecondary} />
        <Text style={infoStyles.label}>{label}</Text>
      </View>
      <Text style={infoStyles.value} numberOfLines={2}>{value}</Text>
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
    maxWidth: '50%',
    textAlign: 'right',
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
  avatarContainer: {
    alignItems: 'center',
    marginBottom: Spacing.md,
  },
  avatar: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: Colors.primary,
    justifyContent: 'center',
    alignItems: 'center',
  },
  avatarText: {
    fontSize: FontSize.title,
    fontWeight: '800',
    color: '#FFFFFF',
  },
  customerName: {
    fontSize: FontSize.xl,
    fontWeight: '700',
    color: Colors.text,
    textAlign: 'center',
  },
  contactRow: {
    flexDirection: 'row',
    justifyContent: 'center',
    gap: Spacing.md,
    marginTop: Spacing.xl,
  },
  contactBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    paddingHorizontal: Spacing.lg,
    paddingVertical: Spacing.sm,
    borderRadius: BorderRadius.full,
  },
  contactBtnText: {
    fontSize: FontSize.sm,
    fontWeight: '600',
  },
  sectionTitle: {
    fontSize: FontSize.lg,
    fontWeight: '700',
    color: Colors.text,
    marginBottom: Spacing.md,
  },
  statsGrid: {
    flexDirection: 'row',
    justifyContent: 'space-around',
  },
  statItem: {
    alignItems: 'center',
    gap: 4,
  },
  statValue: {
    fontSize: FontSize.md,
    fontWeight: '700',
    color: Colors.text,
  },
  statLabel: {
    fontSize: FontSize.xs,
    color: Colors.textMuted,
  },
});
