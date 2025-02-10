<template>
    <div>
      <button @click="toggleDropdown" class="icon-button">
        <svg><!-- Notification icon --></svg>
        <span v-if="unreadCount > 0" class="notification-badge">{{ unreadCount }}</span>
      </button>
  
      <div v-if="isOpen" class="notification-dropdown">
        <div v-if="notifications.length === 0" class="notification-item">
          <p>Nothing to show.</p>
        </div>
        <div v-else v-for="notification in notifications" :key="notification.id" class="notification-item">
          <p v-if="notification.type === 'like'">Someone liked your post.</p>
          <p v-else-if="notification.type === 'comment'">Someone commented on your post.</p>
          <small>{{ formatDate(notification.created_at) }}</small>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      notifications: Array,
      unreadCount: Number
    },
    data() {
      return {
        isOpen: false
      };
    },
    methods: {
      toggleDropdown() {
        this.isOpen = !this.isOpen;
        this.$emit('toggle');
      },
      formatDate(date) {
        return new Date(date).toLocaleString();
      }
    }
  };
  </script>
  
  <style scoped>
  .notification-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #ccc;
    width: 300px;
    max-height: 400px;
    overflow-y: auto;
    z-index: 1000;
  }
  
  .notification-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
  }
  
  .notification-item:last-child {
    border-bottom: none;
  }
  </style>