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

// Maximum number of retries for failed requests
const MAX_RETRIES = 3;
// Timeout in milliseconds
const REQUEST_TIMEOUT = 10000; // 10 seconds

// Background sync using axios
export const syncDashboardData = async (retryCount = 0): Promise<DashboardData | null> => {
  try {
    await axios.get('/sanctum/csrf-cookie');
    
    const response = await axios.get(route('api.user.dashboard-data'), {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      withCredentials: true,
      timeout: REQUEST_TIMEOUT
    });

    return response.data;
  } catch (error) {
    if (axios.isAxiosError(error)) {
      if (error.code === 'ECONNABORTED' && retryCount < MAX_RETRIES) {
        console.warn(`Request timed out, retrying... (${retryCount + 1}/${MAX_RETRIES})`);
        return syncDashboardData(retryCount + 1);
      }
      
      if (error.response) {
        console.error('Error syncing dashboard data:', {
          status: error.response.status,
          message: error.response.data?.message || error.message
        });
      } else if (error.request) {
        console.error('No response received:', error.message);
      } else {
        console.error('Error setting up request:', error.message);
      }
    } else {
      console.error('Unexpected error:', error);
    }
    return null;
  }
};