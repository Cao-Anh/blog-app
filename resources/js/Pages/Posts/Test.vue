<template>
  <div>
    <Navbar
      :notifications="notifications"
      :unread-notifications="unreadNotifications"
      @search="performSearch"
      @toggle-notifications="toggleNotificationDropdown"
      @navigate-home="navigateToHome"
      @navigate-login="navigateToLogin"
      @format-date="formatDate(date)"
      
    />

    <div class="main-content">
      <Sidebar
        :all-tags="allTags"
        @tag-click="navigateToTag"
      />

      <!-- <PostList
        :posts="visiblePosts"
        :is-loading="isLoading"
        :current-page="currentPage"
        :last-page="lastPage"
        @load-more="loadMorePosts"
        @create-post="createNewPost"
        @toggle-like="toggleLike"
        @toggle-comments="toggleCommentSection"
        @submit-comment="submitComment"
        @delete-comment="deleteComment"
        @edit-post="editPost"
        @delete-post="deletePost"
        @open-image-modal="openImageModal"
      /> -->

      <!-- <ImageModal
        v-if="isModalOpen"
        :images="modalImages"
        :current-index="currentImageIndex"
        @close="closeImageModal"
        @prev-image="prevImage"
        @next-image="nextImage"
      /> -->
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { router } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar/Navbar.vue';
import Sidebar from '@/Components/Sidebar/Sidebar.vue';
// import PostList from '@/Components/Post/PostList.vue';
// import ImageModal from '@/Components/Post/ImageModal.vue';
import usePosts from '@/Composables/usePosts';
import useNotifications from '@/Composables/useNotifications';
import useImages from '@/Composables/useImages';

const props = defineProps({
  posts: Object,
  allTags: Array
});

// Composables
const {
  visiblePosts,
  isLoading,
  currentPage,
  lastPage,
  loadMorePosts,
  performSearch,
  navigateToTag,
  toggleCommentSection,
  submitComment,
  deleteComment,
  toggleLike,
  createNewPost,
  editPost,
  deletePost
} = usePosts(props);

const {
  notifications,
  unreadNotifications,
  toggleNotificationDropdown,
  markNotificationsAsRead
} = useNotifications();

const {
  isModalOpen,
  modalImages,
  currentImageIndex,
  openImageModal,
  closeImageModal,
  prevImage,
  nextImage
} = useImages();

// Navigation methods
const navigateToHome = () => router.visit('/');
const navigateToLogin = () => router.visit(route('login'));
const formatDate =(date) => new Date(date).toLocaleString();
// Infinite scroll logic
const handleScroll = () => {
  const bottomOfWindow =
    document.documentElement.scrollTop + window.innerHeight >=
    document.documentElement.offsetHeight - 100;
  if (bottomOfWindow) {
    loadMorePosts();
  }
};

// Real-time notifications
onMounted(() => {
  const currentUser = this.$page.props.auth.user; // Assuming auth.user is passed as a prop
  if (currentUser) {
    console.log(`Listening for notifications on: notifications.${currentUser.id}`);

    window.Echo.private(`notifications.${currentUser.id}`)
      .listen('.comment.added', (e) => {
        console.log('Received Comment Notification:', e); // Debugging log
        if (e.notification) {
          notifications.value.unshift(e.notification);
          unreadNotifications.value++;
        } else {
          console.warn("Received event, but no notification data.");
        }
      })
      .listen('.post.liked', (e) => {
        console.log('Received Like Notification:', e); // Debugging log
        if (e.notification) {
          notifications.value.unshift(e.notification);
          unreadNotifications.value++;
        } else {
          console.warn("Received event, but no notification data.");
        }
      });
  } else {
    console.warn("Log in to check your notifications.");
  }

  window.addEventListener('scroll', handleScroll);
});

onBeforeUnmount(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>



<style>
/* Navbar Styles */
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
  position: absolute;
  top: -5px;
  right: -5px;
  background: red;
  color: white;
  border-radius: 50%;
  padding: 2px 6px;
  font-size: 10px;
}

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

/* Main Content Styles */
.main-content {
  display: flex;
  gap: 24px;
  padding: 16px;
  margin-top: 80px;
}

.sidebar {
  position: fixed;
  top: 60px;
  left: 0;
  width: 20%;
  height: 100vh;
  background: #fff;
  border-right: 1px solid #ddd;
  padding: 16px;
  box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
  overflow-y: auto;
}

.sidebar h3 {
  margin-bottom: 12px;
  font-size: 1.2em;
  color: #333;
}

.tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.tag {
  background: #f0f0f0;
  padding: 4px 8px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9em;
  color: #333;
  transition: background 0.2s, color 0.2s;
}

.tag:hover {
  background: #ddd;
  color: #000;
}

/* Existing Post Styles (unchanged) */
.post-list {
  max-width: 600px;
  min-width: 450px;
  margin: 0 auto;
}

.post {
  position: relative;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  margin-bottom: 16px;
  padding: 16px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.author-section {
  display: flex;
  align-items: center;
  margin-bottom: 12px;
}

.profile-picture {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 12px;
}

.author-name {
  font-weight: bold;
}

.author-name:hover {
  text-decoration: underline;
}

.timestamp {
  color: #666;
  font-size: 0.9em;
}

.post-content {
  margin-bottom: 12px;
}

.post-images {
  display: grid;
  gap: 8px;
  margin-bottom: 12px;
}

.image {
  width: 100%;
  border-radius: 8px;
}

.actions {
  display: flex;
  gap: 16px;
  margin-bottom: 12px;
}

.actions button {
  background: none;
  border: none;
  cursor: pointer;
  color: #666;
}

.actions button.liked {
  color: #1877f2;
  font-weight: bold;
}

.comment-section {
  margin-top: 12px;
}

.comment-section textarea {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-bottom: 8px;
}

.comments {
  margin-top: 12px;
}

.comment {
  display: flex;
  align-items: flex-start;
  margin-bottom: 8px;
}

.comment-content {
  flex: 1;
}

.comment-author {
  font-weight: bold;
}


.admin-actions {
  position: absolute;
  top: 16px;
  right: 16px;
  display: flex;
  gap: 8px;
}

.delete-button {
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
  transition: color 0.2s;
}

.delete-button:hover {
  color: #ff0000;
}

.delete-button svg {
  fill: currentColor;
}

.icon-button {
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
  transition: color 0.2s;
}

.icon-button:hover {
  color: #1877f2;
}

.icon-button svg {
  fill: currentColor;
}

.loading,
.no-more-posts {
  text-align: center;
  padding: 16px;
  font-size: 0.9em;
  color: #666;
}

/* Post Images */
.folded-images {
  position: relative;
  display: inline-block;
}

.image {
  width: 100%;
  border-radius: 8px;
  cursor: pointer;
}

.extra-images-overlay {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background: rgba(0, 0, 0, 0.5);
  color: #fff;
  font-size: 1.5em;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  cursor: pointer;
}

/* Modal Styles */
.image-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-image {
  max-width: 90%;
  max-height: 80%;
  border-radius: 8px;
}

.close-modal {
  position: absolute;
  top: 16px;
  right: 16px;
  background: none;
  border: none;
  color: #fff;
  font-size: 2em;
  cursor: pointer;
}

.nav-button {
  position: absolute;
  top: 50%;
  background: none;
  border: none;
  color: #fff;
  font-size: 10em;
  cursor: pointer;
  transform: translateY(-50%);
}

.prev {
  left: 30px;
}

.next {
  right: 30px;
}
</style>
