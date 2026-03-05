import React, { useState, useEffect } from 'react';
import { View, Text, ScrollView, StyleSheet, TextInput, TouchableOpacity, Alert, ActivityIndicator } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import * as Haptics from 'expo-haptics';
import { Colors, Spacing, BorderRadius, Shadow, FontSize } from '../theme';
import api from '../api/client';

export default function SettingsScreen() {
  const [apiUrl, setApiUrl] = useState('');
  const [tempUrl, setTempUrl] = useState('');
  const [testing, setTesting] = useState(false);
  const [connected, setConnected] = useState(null); // null | true | false

  useEffect(() => {
    loadSettings();
  }, []);

  const loadSettings = async () => {
    const url = await api.getBaseUrl();
    setApiUrl(url);
    setTempUrl(url);
  };

  const testConnection = async () => {
    setTesting(true);
    setConnected(null);
    try {
      await api.setBaseUrl(tempUrl);
      const result = await api.testConnection();
      setConnected(true);
      setApiUrl(tempUrl);
      Haptics.notificationAsync(Haptics.NotificationFeedbackType.Success);
      Alert.alert('Başarılı', 'Sunucuya bağlantı başarılı!');
    } catch (error) {
      setConnected(false);
      Haptics.notificationAsync(Haptics.NotificationFeedbackType.Error);
      Alert.alert('Hata', 'Sunucuya bağlanılamadı. URL\'yi kontrol edin.');
    } finally {
      setTesting(false);
    }
  };

  const resetUrl = () => {
    const defaultUrl = 'http://192.168.1.100:8000';
    setTempUrl(defaultUrl);
  };

  return (
    <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
      {/* API Ayarları */}
      <View style={[styles.card, Shadow.md]}>
        <View style={styles.cardHeader}>
          <Ionicons name="server-outline" size={22} color={Colors.primary} />
          <Text style={styles.cardTitle}>Sunucu Bağlantısı</Text>
        </View>

        <Text style={styles.label}>API Sunucu Adresi</Text>
        <View style={styles.inputRow}>
          <TextInput
            style={styles.input}
            value={tempUrl}
            onChangeText={setTempUrl}
            placeholder="http://192.168.1.100:8000"
            placeholderTextColor={Colors.textMuted}
            autoCapitalize="none"
            autoCorrect={false}
            keyboardType="url"
          />
          <TouchableOpacity style={styles.resetBtn} onPress={resetUrl}>
            <Ionicons name="refresh-outline" size={20} color={Colors.textSecondary} />
          </TouchableOpacity>
        </View>

        <TouchableOpacity
          style={[styles.testBtn, testing && styles.testBtnDisabled]}
          onPress={testConnection}
          disabled={testing}
        >
          {testing ? (
            <ActivityIndicator size="small" color="#FFFFFF" />
          ) : (
            <>
              <Ionicons name="flash-outline" size={18} color="#FFFFFF" />
              <Text style={styles.testBtnText}>Bağlantıyı Test Et</Text>
            </>
          )}
        </TouchableOpacity>

        {connected !== null && (
          <View style={[styles.statusRow, { backgroundColor: connected ? Colors.successLight : Colors.dangerLight }]}>
            <Ionicons
              name={connected ? 'checkmark-circle' : 'close-circle'}
              size={18}
              color={connected ? Colors.success : Colors.danger}
            />
            <Text style={[styles.statusText, { color: connected ? Colors.success : Colors.danger }]}>
              {connected ? 'Bağlantı başarılı' : 'Bağlantı başarısız'}
            </Text>
          </View>
        )}

        <Text style={styles.helpText}>
          Laravel sunucunuzun çalıştığı IP adresi ve portu girin.{'\n'}
          Örnek: http://192.168.1.100:8000
        </Text>
      </View>

      {/* Bağlantı İpuçları */}
      <View style={[styles.card, Shadow.md]}>
        <View style={styles.cardHeader}>
          <Ionicons name="bulb-outline" size={22} color={Colors.warning} />
          <Text style={styles.cardTitle}>Bağlantı İpuçları</Text>
        </View>
        <TipRow
          icon="desktop-outline"
          text="Laravel sunucusunu 'php artisan serve --host=0.0.0.0' ile başlatın"
        />
        <TipRow
          icon="wifi-outline"
          text="Telefon ve bilgisayar aynı Wi-Fi ağında olmalı"
        />
        <TipRow
          icon="terminal-outline"
          text="Bilgisayarınızın IP adresini 'ifconfig' komutuyla bulabilirsiniz"
        />
        <TipRow
          icon="shield-outline"
          text="Güvenlik duvarı 8000 portuna izin vermelidir"
        />
      </View>

      {/* Uygulama Hakkında */}
      <View style={[styles.card, Shadow.md]}>
        <View style={styles.cardHeader}>
          <Ionicons name="information-circle-outline" size={22} color={Colors.info} />
          <Text style={styles.cardTitle}>Uygulama Hakkında</Text>
        </View>
        <InfoRow label="Uygulama" value="Emare Finance" />
        <InfoRow label="Sürüm" value="1.0.0" />
        <InfoRow label="Platform" value="React Native (Expo)" />
        <InfoRow label="Geliştirici" value="Emare" />
      </View>

      <View style={{ height: 30 }} />
    </ScrollView>
  );
}

function TipRow({ icon, text }) {
  return (
    <View style={tipStyles.row}>
      <Ionicons name={icon} size={16} color={Colors.textSecondary} />
      <Text style={tipStyles.text}>{text}</Text>
    </View>
  );
}

function InfoRow({ label, value }) {
  return (
    <View style={infoRowStyles.row}>
      <Text style={infoRowStyles.label}>{label}</Text>
      <Text style={infoRowStyles.value}>{value}</Text>
    </View>
  );
}

const tipStyles = StyleSheet.create({
  row: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    gap: Spacing.sm,
    paddingVertical: Spacing.sm,
    borderBottomWidth: 1,
    borderBottomColor: Colors.borderLight,
  },
  text: {
    flex: 1,
    fontSize: FontSize.sm,
    color: Colors.textSecondary,
    lineHeight: 20,
  },
});

const infoRowStyles = StyleSheet.create({
  row: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: Spacing.sm,
    borderBottomWidth: 1,
    borderBottomColor: Colors.borderLight,
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
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: Spacing.sm,
    marginBottom: Spacing.lg,
  },
  cardTitle: {
    fontSize: FontSize.lg,
    fontWeight: '700',
    color: Colors.text,
  },
  label: {
    fontSize: FontSize.sm,
    fontWeight: '600',
    color: Colors.textSecondary,
    marginBottom: Spacing.sm,
  },
  inputRow: {
    flexDirection: 'row',
    gap: Spacing.sm,
    marginBottom: Spacing.md,
  },
  input: {
    flex: 1,
    backgroundColor: Colors.bg,
    borderRadius: BorderRadius.md,
    paddingHorizontal: Spacing.lg,
    paddingVertical: Spacing.md,
    fontSize: FontSize.md,
    color: Colors.text,
    borderWidth: 1,
    borderColor: Colors.border,
  },
  resetBtn: {
    width: 44,
    height: 44,
    borderRadius: BorderRadius.md,
    backgroundColor: Colors.bg,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: Colors.border,
  },
  testBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: Spacing.sm,
    backgroundColor: Colors.primary,
    paddingVertical: Spacing.md,
    borderRadius: BorderRadius.md,
  },
  testBtnDisabled: {
    opacity: 0.7,
  },
  testBtnText: {
    fontSize: FontSize.md,
    fontWeight: '700',
    color: '#FFFFFF',
  },
  statusRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: Spacing.sm,
    padding: Spacing.md,
    borderRadius: BorderRadius.md,
    marginTop: Spacing.md,
  },
  statusText: {
    fontSize: FontSize.sm,
    fontWeight: '600',
  },
  helpText: {
    fontSize: FontSize.xs,
    color: Colors.textMuted,
    marginTop: Spacing.md,
    lineHeight: 18,
  },
});
