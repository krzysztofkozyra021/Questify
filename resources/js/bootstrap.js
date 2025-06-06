import axios from 'axios'

window.axios = axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.axios.defaults.withCredentials = true

// Set the base URL for all axios requests
window.axios.defaults.baseURL = import.meta.env.VITE_APP_URL || window.location.origin
