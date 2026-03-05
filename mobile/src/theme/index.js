// Emare Finance - Renk ve stil tanımları
export const Colors = {
  primary: '#4F46E5',
  primaryDark: '#3730A3',
  primaryLight: '#EEF2FF',
  primaryMid: '#818CF8',

  success: '#10B981',
  successDark: '#059669',
  successLight: '#D1FAE5',

  warning: '#F59E0B',
  warningDark: '#D97706',
  warningLight: '#FEF3C7',

  danger: '#EF4444',
  dangerDark: '#DC2626',
  dangerLight: '#FEE2E2',

  info: '#3B82F6',
  infoDark: '#2563EB',
  infoLight: '#DBEAFE',

  bg: '#F3F4F6',
  card: '#FFFFFF',
  border: '#E5E7EB',
  borderLight: '#F3F4F6',

  text: '#1F2937',
  textSecondary: '#6B7280',
  textMuted: '#9CA3AF',
  textLight: '#D1D5DB',
  white: '#FFFFFF',
  black: '#111827',

  // Ödeme yöntemleri renkleri
  paymentCash: '#10B981',
  paymentCard: '#3B82F6',
  paymentCredit: '#F59E0B',
  paymentMixed: '#8B5CF6',

  tabActive: '#4F46E5',
  tabInactive: '#9CA3AF',
};

export const Spacing = {
  xs: 4,
  sm: 8,
  md: 12,
  lg: 16,
  xl: 20,
  xxl: 24,
  xxxl: 32,
};

export const FontSize = {
  xs: 10,
  sm: 12,
  md: 14,
  lg: 16,
  xl: 18,
  xxl: 22,
  xxxl: 28,
  title: 32,
};

export const BorderRadius = {
  sm: 8,
  md: 12,
  lg: 16,
  xl: 20,
  full: 999,
};

export const Shadow = {
  sm: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 2,
    elevation: 1,
  },
  md: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 4,
    elevation: 3,
  },
  lg: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.12,
    shadowRadius: 8,
    elevation: 5,
  },
};
