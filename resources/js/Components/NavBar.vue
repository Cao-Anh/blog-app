<template>
    <nav class="navbar">
        <div class="navbar-left">
            <!-- Logo -->
            <img src="/images/laravel-logo.png" alt="Laravel Logo" class="logo" />
            <!-- Home Button -->
            <button @click="navigateToHome" class="icon-button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                </svg>
            </button>
        </div>

        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" v-model="searchQuery" placeholder="Search..." />
            <button @click="performSearch" class="icon-button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                    <path
                        d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                </svg>
            </button>
        </div>

        <!-- Notification and User Section -->
        <div class="navbar-right">
            <!-- Notification Bell -->
            <button @click="showNotifications" class="icon-button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                    <path
                        d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
                </svg>
                <span v-if="unreadNotifications > 0" class="notification-badge">{{ unreadNotifications }}</span>
            </button>

            <!-- User Login/Name -->
            <div v-if="$page.props.auth.user" class="user-section">
                <img :src="`/storage/${$page.props.auth.user.profile_picture}`" alt="profile picture"
                    class="profile-picture">
            </div>
            <button v-else @click="navigateToLogin" class="login-button">Login</button>
        </div>
    </nav>
</template>
<script>
export default {
    data() {
        return {
            searchQuery: '',
            unreadNotifications: 0,
        };
    },
    methods: {
        navigateToHome() {
            this.$inertia.visit('/');
        },
        performSearch() {
            console.log('Searching for:', this.searchQuery);
        },
        showNotifications() {
            console.log('Showing notifications');
        },
        navigateToLogin() {
            this.$inertia.visit(route('login'));
        },
    },
};
</script>
<style scoped>
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background: #fff;
    border-bottom: 1px solid #ddd;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}

.navbar-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.logo {
    height: 40px;
    width: auto;
}

.search-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-grow: 1;
    max-width: 400px;
    margin: 0 16px;
}

.search-bar input {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 100%;
}

.navbar-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

.notification-button {
    position: relative;
}

.notification-badge {
    background: #f44336;
    color: #fff;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 0.8em;
    position: absolute;
    top: -8px;
    right: -8px;
}

.user-section {
    display: flex;
    align-items: center;
    gap: 8px;
}

.username {
    font-weight: bold;
}

.login-button {
    background: #1877f2;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.2s;
}

.login-button:hover {
    background: #155a9d;
}
</style>