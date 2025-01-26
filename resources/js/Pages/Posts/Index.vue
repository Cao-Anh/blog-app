<template>
  <div>
    <!-- Navigation Bar -->
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
          <img :src="`/storage/${$page.props.auth.user.profile_picture}`" alt="profile picture" class="profile-picture">
        </div>
        <button v-else @click="navigateToLogin" class="login-button">Login</button>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Left Sidebar for Tags -->
      <div class="sidebar">
        <h3>Tags</h3>
        <div class="tags">
          <span v-for="tag in allTags" :key="tag.id" @click="navigateToTag(tag)" class="tag">{{ tag.name }}</span>
        </div>
      </div>

      <!-- Post List -->
      <div class="post-list">
        <h1>Posts</h1>
        <button @click="createNewPost" class="btn">Create New Post</button>

        <div v-for="post in posts" :key="post.id" class="post">
          <!-- Admin Actions (Top Right) -->
          <div v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'" class="admin-actions">
            <button @click="editPost(post)" class="icon-button">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16">
                <path
                  d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a.996.996 0 0 0 0-1.41l-2.34-2.34a.996.996 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
              </svg>
            </button>
            <button @click="deletePost(post.id)" class="delete-button">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16">
                <path
                  d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" />
              </svg>
            </button>
          </div>

          <!-- Author Section -->
          <div class="author-section">
            <img :src="`/storage/${post.author.profile_picture}`" alt="Profile Picture" class="profile-picture" />
            <div class="author-info">
              <span class="author-name">{{ post.author.name }}</span>
              <span class="timestamp">{{ formatDate(post.created_at) }}</span>
            </div>
          </div>

          <!-- Post Content -->
          <div class="post-content">
            <p>{{ post.title }}</p>
            <p>{{ post.body }}</p>
          </div>

          <!-- Tags -->
          <div v-if="post.tags.length > 0" class="tags">
            <span v-for="tag in post.tags" :key="tag.id" @click="navigateToTag(tag)" class="tag">{{ tag.name }}</span>
          </div>

          <!-- Images -->
          <div v-if="post.images.length > 0" class="post-images">
            <img v-for="image in post.images" :key="image.id" :src="`/storage/${image.path}`" alt="Post Image"
              class="image" />
          </div>

          <!-- Like and Comment Section -->
          <div class="actions">
            <button @click="toggleLike(post)" :class="{ 'liked': post.is_liked }">
              {{ post.is_liked ? 'Liked' : 'Like' }} ({{ post.likes_count }})
            </button>
            <button @click="toggleCommentSection(post)">
              Comment ({{ post.comments.length }})
            </button>
          </div>

          <!-- Comment Section -->
          <div v-if="post.showComments" class="comment-section">
            <form @submit.prevent="submitComment(post)">
              <textarea v-model="post.commentContent" placeholder="Add a comment"></textarea>
              <button type="submit">Submit</button>
            </form>

            <!-- Display Comments -->
            <div v-if="post.comments.length > 0" class="comments">
              <div v-for="comment in post.comments" :key="comment.id" class="comment">
                <img :src="`/storage/${comment.user.profile_picture}`" alt="Profile Picture" class="profile-picture" />
                <div class="comment-content">
                  <span class="comment-author">{{ comment.user.name }}</span>
                  <p>{{ comment.content }}</p>
                  <small>{{ formatDate(comment.created_at) }}</small>
                </div>
                <button
                  v-if="$page.props.auth.user && ($page.props.auth.user.id === comment.user_id || $page.props.auth.user.role === 'admin')"
                  @click="deleteComment(comment)" class="delete-button">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16">
                    <path
                      d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link, router } from '@inertiajs/vue3';
import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';
import Swal from 'sweetalert2';
import axios from 'axios';

export default {
  components: { Link },
  props: {
    posts: Array,
    allTags: Array, // Add allTags as a prop
  },
  data() {
    return {
      searchQuery: '',
      unreadNotifications: 0, // Placeholder for notification count
    };
  },
  created() {
    this.posts.forEach(post => {
      post.commentContent = '';
      post.showComments = false; // Add a flag to toggle comment section
    });
  },
  methods: {
    navigateToHome() {
      this.$inertia.visit('/');
    },
    performSearch() {
      // Implement search functionality
      console.log('Searching for:', this.searchQuery);
    },
    showNotifications() {
      // Implement notification functionality
      console.log('Showing notifications');
    },
    navigateToLogin() {
      this.$inertia.visit(route('login', { redirect: this.currentUrl }));
    },
    navigateToTag(tag) {
      // Navigate to the tag page (placeholder for now)
      this.$inertia.visit('/');
    },
    formatDate(date) {
      return new Date(date).toLocaleString();
    },
    toggleCommentSection(post) {
      post.showComments = !post.showComments;
    },
    async submitComment(post) {
      if (!this.$page.props.auth.user) {
        Swal.fire({
          title: 'Login Required',
          text: 'You need to log in to comment. Do you want to log in now?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, log in',
          cancelButtonText: 'Cancel',
        }).then((result) => {
          if (result.isConfirmed) {
            this.$inertia.visit(route('login', { redirect: this.currentUrl }));
          }
        });
        return;
      }

      try {
        const response = await axios.post(route('comments.store', { post: post.id }), {
          content: post.commentContent,
        });

        if (response.data.comment) {
          post.comments.push(response.data.comment);
        }
        post.commentContent = '';
      } catch (error) {
        console.error('Error submitting comment:', error);
        Swal.fire({
          title: 'Error',
          text: 'Failed to submit the comment. Please try again.',
          icon: 'error',
        });
      }
    },
    deleteComment(comment) {
      this.$inertia.delete(route('comments.destroy', comment.id));
    },
    async toggleLike(post) {
      if (!this.$page.props.auth.user) {
        Swal.fire({
          title: 'Login Required',
          text: 'You need to log in to like a post. Do you want to log in now?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, log in',
          cancelButtonText: 'Cancel',
        }).then((result) => {
          if (result.isConfirmed) {
            this.$inertia.visit(route('login', { redirect: this.currentUrl }));
          }
        });
        return;
      }

      const wasLiked = post.is_liked;
      post.is_liked = !post.is_liked;
      post.likes_count += post.is_liked ? 1 : -1;

      try {
        await axios.post(route('posts.like', post.id));
      } catch (error) {
        post.is_liked = wasLiked;
        post.likes_count += post.is_liked ? 1 : -1;
        console.error('Error toggling like:', error);
      }
    },
    createNewPost() {
      if (this.$page.props.auth.user) {
        this.$inertia.visit(route('posts.create'));
      } else {
        Swal.fire({
          title: 'Login Required',
          text: 'You need to log in to create a new post. Do you want to log in now?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, log in',
          cancelButtonText: 'Cancel',
        }).then((result) => {
          if (result.isConfirmed) {
            this.$inertia.visit(route('login'));
          }
        });
      }
    },
    editPost(post) {
      const currentUser = this.$page.props.auth.user;
      if (currentUser && (currentUser.role === 'admin' || post.author_id === currentUser.id)) {
        router.visit(`/posts/${post.id}/edit`);
      } else {
        Toastify({
          text: 'You are not authorized to edit this post.',
          duration: 3000,
          close: true,
          gravity: 'top',
          position: 'right',
          backgroundColor: '#F44336',
        }).showToast();
      }
    },
    deletePost(id) {
      if (confirm('Are you sure you want to delete this post?')) {
        this.$inertia.delete(`/posts/${id}`);
      }
    },
  },
  mounted() {
    this.currentUrl = window.location.href;
  },
};
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
</style>
