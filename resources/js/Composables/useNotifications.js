import { ref } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { router } from '@inertiajs/vue3';

export default function useNotifications() {
  const notifications = ref([]);
  const unreadNotifications = ref(0);
  const isNotificationDropdownOpen = ref(false);

  const fetchNotifications = async (userId) => {
    try {
      const response = await axios.get(`/api/notifications?user_id=${userId}`);
      notifications.value = [...response.data];
      unreadNotifications.value = notifications.value.filter(n => !n.read).length;
    } catch (error) {
      console.error("Error fetching notifications:", error);
      notifications.value = [];
    }
  };

  const markNotificationsAsRead = async (userId) => {
    if (!userId) {
      console.warn("User is not authenticated.");
      return;
    }
    try {
      await axios.post('/api/notifications/mark-as-read', { user_id: userId });
      if (Array.isArray(notifications.value)) {
        notifications.value.forEach(n => (n.read = true));
        unreadNotifications.value = 0;
      } else {
        console.warn("Notifications array is not defined.");
      }
    } catch (error) {
      console.error("Error marking notifications as read:", error);
    }
  };

  const toggleNotificationDropdown = () => {
    isNotificationDropdownOpen.value = !isNotificationDropdownOpen.value;
  };

  const showNotifications = (currentUser) => {
    if (!currentUser) {
      Swal.fire({
        title: 'Login Required',
        text: 'Log in to see your notifications. Do you want to log in now?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, log in',
        cancelButtonText: 'Cancel',
      }).then((result) => {
        if (result.isConfirmed) {
          router.visit(route('login'));
        }
      });
    } else {
      toggleNotificationDropdown();
      markNotificationsAsRead(currentUser.id);
    }
  };

  return {
    notifications,
    unreadNotifications,
    isNotificationDropdownOpen,
    fetchNotifications,
    markNotificationsAsRead,
    toggleNotificationDropdown,
    showNotifications,
  };
}