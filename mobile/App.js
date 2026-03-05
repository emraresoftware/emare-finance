import React, { useEffect } from 'react';
import { StatusBar, Platform, LogBox } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { Colors, FontSize } from './src/theme';
import api from './src/api/client';

// Screens
import DashboardScreen from './src/screens/DashboardScreen';
import SalesScreen from './src/screens/SalesScreen';
import SaleDetailScreen from './src/screens/SaleDetailScreen';
import ProductsScreen from './src/screens/ProductsScreen';
import ProductDetailScreen from './src/screens/ProductDetailScreen';
import CustomersScreen from './src/screens/CustomersScreen';
import CustomerDetailScreen from './src/screens/CustomerDetailScreen';
import MoreScreen from './src/screens/MoreScreen';
import ReportsScreen from './src/screens/ReportsScreen';
import StockScreen from './src/screens/StockScreen';
import SettingsScreen from './src/screens/SettingsScreen';

// Suppress known warnings
LogBox.ignoreLogs(['Non-serializable values were found in the navigation state']);

const Tab = createBottomTabNavigator();
const Stack = createNativeStackNavigator();

const screenOptions = {
  headerStyle: {
    backgroundColor: Colors.card,
    elevation: 0,
    shadowOpacity: 0,
    borderBottomWidth: 1,
    borderBottomColor: Colors.border,
  },
  headerTintColor: Colors.text,
  headerTitleStyle: {
    fontWeight: '700',
    fontSize: FontSize.lg,
  },
  contentStyle: {
    backgroundColor: Colors.bg,
  },
};

// ========== Stack Navigators ==========

function DashboardStack() {
  return (
    <Stack.Navigator screenOptions={screenOptions}>
      <Stack.Screen name="DashboardHome" component={DashboardScreen} options={{ title: 'Ana Sayfa' }} />
    </Stack.Navigator>
  );
}

function SalesStack() {
  return (
    <Stack.Navigator screenOptions={screenOptions}>
      <Stack.Screen name="SalesList" component={SalesScreen} options={{ title: 'Satışlar' }} />
      <Stack.Screen name="SaleDetail" component={SaleDetailScreen} options={{ title: 'Satış Detayı' }} />
    </Stack.Navigator>
  );
}

function ProductsStack() {
  return (
    <Stack.Navigator screenOptions={screenOptions}>
      <Stack.Screen name="ProductsList" component={ProductsScreen} options={{ title: 'Ürünler' }} />
      <Stack.Screen name="ProductDetail" component={ProductDetailScreen} options={{ title: 'Ürün Detayı' }} />
    </Stack.Navigator>
  );
}

function CustomersStack() {
  return (
    <Stack.Navigator screenOptions={screenOptions}>
      <Stack.Screen name="CustomersList" component={CustomersScreen} options={{ title: 'Cariler' }} />
      <Stack.Screen name="CustomerDetail" component={CustomerDetailScreen} options={{ title: 'Müşteri Detayı' }} />
      <Stack.Screen name="SaleDetail" component={SaleDetailScreen} options={{ title: 'Satış Detayı' }} />
    </Stack.Navigator>
  );
}

function MoreStack() {
  return (
    <Stack.Navigator screenOptions={screenOptions}>
      <Stack.Screen name="MoreHome" component={MoreScreen} options={{ title: 'Daha Fazla' }} />
      <Stack.Screen name="Reports" component={ReportsScreen} options={{ title: 'Raporlar' }} />
      <Stack.Screen name="Stock" component={StockScreen} options={{ title: 'Stok Yönetimi' }} />
      <Stack.Screen name="Settings" component={SettingsScreen} options={{ title: 'Ayarlar' }} />
    </Stack.Navigator>
  );
}

// ========== Tab Navigator ==========

const tabIcons = {
  Dashboard: { focused: 'home', unfocused: 'home-outline' },
  Sales: { focused: 'receipt', unfocused: 'receipt-outline' },
  Products: { focused: 'cube', unfocused: 'cube-outline' },
  Customers: { focused: 'people', unfocused: 'people-outline' },
  More: { focused: 'menu', unfocused: 'menu-outline' },
};

export default function App() {
  useEffect(() => {
    api.init();
  }, []);

  return (
    <>
      <StatusBar barStyle="dark-content" backgroundColor={Colors.card} />
      <NavigationContainer>
        <Tab.Navigator
          screenOptions={({ route }) => ({
            headerShown: false,
            tabBarIcon: ({ focused, color, size }) => {
              const icons = tabIcons[route.name];
              const iconName = focused ? icons.focused : icons.unfocused;
              return <Ionicons name={iconName} size={size} color={color} />;
            },
            tabBarActiveTintColor: Colors.primary,
            tabBarInactiveTintColor: Colors.textMuted,
            tabBarStyle: {
              backgroundColor: Colors.card,
              borderTopColor: Colors.border,
              borderTopWidth: 1,
              height: Platform.OS === 'ios' ? 88 : 64,
              paddingBottom: Platform.OS === 'ios' ? 28 : 8,
              paddingTop: 8,
              elevation: 0,
            },
            tabBarLabelStyle: {
              fontSize: 11,
              fontWeight: '600',
            },
          })}
        >
          <Tab.Screen name="Dashboard" component={DashboardStack} options={{ tabBarLabel: 'Ana Sayfa' }} />
          <Tab.Screen name="Sales" component={SalesStack} options={{ tabBarLabel: 'Satışlar' }} />
          <Tab.Screen name="Products" component={ProductsStack} options={{ tabBarLabel: 'Ürünler' }} />
          <Tab.Screen name="Customers" component={CustomersStack} options={{ tabBarLabel: 'Cariler' }} />
          <Tab.Screen name="More" component={MoreStack} options={{ tabBarLabel: 'Menü' }} />
        </Tab.Navigator>
      </NavigationContainer>
    </>
  );
}
