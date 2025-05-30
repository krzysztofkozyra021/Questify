// syncDashboardData.ts
import axios from "axios";
import { route } from "ziggy-js";

interface DashboardData {
  tasks: {
    habits: any;
    dailies: any;
    todos: any;
  };
  user: any;
  last_sync: number;
}

// Sync interval in seconds
export const SYNC_INTERVAL = 5 * 60; // 5 minutes

// Background sync using axios
export const syncDashboardData = async (): Promise<DashboardData | null> => {
  try {
    await axios.get('/sanctum/csrf-cookie');
    
    const response = await axios.get(route('api.user.dashboard-data'), {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      withCredentials: true
    });

    return response.data;
  } catch (error) {
    console.error('Error syncing dashboard data:', error);
    return null;
  }
};