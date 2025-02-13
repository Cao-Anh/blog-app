<template>
  <div>
    <!-- Navigation Bar -->
    <nav class="navbar">
      <div class="navbar-left">
        <!-- Logo -->
        <a href="/">
          <img src="/images/laravel-logo.png" alt="Laravel Logo" class="logo" />
        </a>

      </div>

      <!-- Search Bar and Home Button -->

      <div class="search-bar">
        <button @click="navigateToHome" class="icon-button mr-10">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30">
            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
          </svg>
        </button>
        <input type="text hidden md:block" v-model="searchQuery" @keyup.enter="performSearch" placeholder="Search..." />
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
        <button @click="showNotifications" ref="bellIcon" class="icon-button notification-button">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
            <path
              d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
          </svg>
          <span v-if="unreadNotifications > 0" class="notification-badge">{{ unreadNotifications }}</span>
        </button>

        <!-- Notification Dropdown -->
        <div v-if="isNotificationDropdownOpen" ref="dropdown" class="notification-dropdown">
          <div v-if="notifications.length === 0" class="notification-item">
            <p>Nothing to show.</p>
          </div>
          <div v-else v-for="notification in visibleNotifications" :key="notification.id" class="notification-item"
            @click="navigateToPost(notification.post_id)">
            <p v-if="notification.type === 'like'">
            <p class="user-name inline">{{ notification.user_name }}</p> liked your post.</p>
            <p v-else-if="notification.type === 'comment'">
            <p class="user-name inline">{{ notification.user_name }}</p> commented on your post.</p>
            <small>{{ formatDate(notification.created_at) }}</small>
          </div>
          <button v-if="notifications.length > visibleNotifications.length" @click="loadMoreNotifications"
            class="see-more-button">See More</button>
        </div>

        <!-- User Login/Name -->
        <div v-if="$page.props.auth.user" class="user-section">
          <user-dropdown>
          </user-dropdown>
        </div>
        <button v-else @click="navigateToLogin" class="login-button">Login</button>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Left Sidebar for Tags -->
      <div class="sidebar">
        <h3><i class="fa-solid fa-tag"></i>Tags</h3>
        <div class="tags">
          <span v-for="tag in allTags" :key="tag.id" @click="navigateToTag(tag)" class="tag">{{ tag.name }}</span>
        </div>
      </div>

      <!-- Post List -->
      <div class="post-list">
        <button class="status-box mb-8" @click="createNewPost">
          <img
            :src="$page.props.auth.user ? `/storage/${$page.props.auth.user.profile_picture}` : '/storage/users/default-image.png'"
            alt="Profile Picture" class="profile-picture" />
          <p class="status-input">What's on your mind?</p>
        </button>

        <div v-for="post in visiblePosts" :key="post.id" class="post">
          <!-- Admin Actions (Top Right) -->
          <div
            v-if="$page.props.auth.user && ($page.props.auth.user.id === post.user_id || $page.props.auth.user.role === 'admin')"
            class="admin-actions">
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
            <img
              :src="post.author?.profile_picture ? `/storage/${post.author.profile_picture}` : '/images/default-profile.png'"
              alt="Profile Picture" class="profile-picture" />

            <div class="author-info">
              <span class="author-name" @click="navigateToAuthorPosts(post.author.id)" style="cursor: pointer;">
                {{ post.author.name }}
              </span>
              <span class="timestamp ml-3">{{ formatDate(post.created_at) }}</span>
            </div>
          </div>

          <!-- Post Content -->
          <div class="post-content">
            <p class="font-bold ">{{ post.title }}</p>
            <p>{{ truncatedText(post.content, post.id) }}
              <button class="text-blue-500" v-if="showSeeMoreButton(post.content, post.id)"
                @click="toggleText(post.id)">
                See more
              </button>
              <button class="text-blue-500" v-if="showSeeLessButton(post.content, post.id)"
                @click="toggleText(post.id)">
                Show Less
              </button>
            </p>

          </div>

          <!-- Tags -->
          <div v-if="post.tags.length > 0" class="tags mb-2">
            <span><i class="fa-solid fa-tag"></i></span>
            <span v-for="tag in post.tags" :key="tag.id" @click="navigateToTag(tag)" class="tag">{{ tag.name }}</span>
          </div>

          <!-- Images -->
          <div class="post-images">
            <div v-if="post.images.length > 0" class="folded-images">
              <!-- Show First Image -->
              <img :src="`/storage/${post.images[0].path}`" alt="Post Image" class="image w-auto h-56"
                @click="openImageModal(post, 0)" />

              <!-- Additional Images Overlay -->
              <div v-if="post.images.length > 1" class="extra-images-overlay" @click="openImageModal(post, 1)">
                +{{ post.images.length - 1 }}
              </div>
            </div>
          </div>

          <!-- Modal for Full-Screen View -->
          <div v-if="isModalOpen" class="image-modal" @click.self="closeImageModal">
            <button class="close-modal" @click="closeImageModal">✕</button>
            <img :src="currentImage" alt="Current Image" class="modal-image" />
            <button v-if="currentImageIndex > 0" class="nav-button prev" @click="prevImage">‹</button>
            <button v-if="currentImageIndex < modalImages.length - 1" class="nav-button next" @click="nextImage">
              ›
            </button>
          </div>

          <!-- Like share and Comment Section -->
          <div class="actions">
            <!-- Like Button -->
            <button @click="toggleLike(post)" :class="{ 'liked': post.is_liked }">
              <i :class="post.is_liked ? 'fas fa-thumbs-up' : 'far fa-thumbs-up'"></i>
              {{ post.is_liked ? 'Liked' : 'Like' }} ({{ post.likes_count }})
            </button>

            <!-- Comment Button -->
            <button @click="toggleCommentSection(post)">
              <i class="far fa-comment"></i>
              Comment ({{ post.comments.length }})
            </button>

            <!-- Share Button -->
            <button @click="sharePost(post.id)">
              <i class="fas fa-share"></i>
              Share
            </button>
          </div>

          <!-- Comment Section -->
          <div v-if="post.showComments" class="comment-section">
            <form @submit.prevent="submitComment(post)">
              <textarea v-model="post.commentContent" placeholder="Add a comment"></textarea>
              <button class="mb-3" type="submit">Send <i class="fa-solid fa-paper-plane fa-rotate-by"
                  style="--fa-rotate-angle: 45deg;"></i></button>
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
        <div v-if="isLoading" class="loading">Loading more posts...</div>
        <div v-if="currentPage >= lastPage" class="no-more-posts">No more posts to load.</div>
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
import { reactive, nextTick, ref } from 'vue';
import UserDropdown from '@/Layouts/UserDropdown.vue';
import { onClickOutside } from '@vueuse/core';

export default {
  components: { Link, UserDropdown, },
  props: {
    posts: Object,
    allTags: Array,
    tag: String,

  },

  data() {
    return {
      currentUrl: window.location.href,
      currentUser: this.$page.props.auth.user,
      searchQuery: '',

      isNotificationDropdownOpen: false,
      notifications: [],
      visibleNotifications: [],
      unreadNotifications: 0,
      page: 1,
      perPage: 5,


      currentTag: null,
      currentPage: this.posts.current_page,
      lastPage: this.posts.last_page,
      visiblePosts: this.posts.data,
      isLoading: false,

      isModalOpen: false,
      modalImages: [],
      currentImage: "",
      currentImageIndex: 0,



      maxLength: 50,
      expandedPosts: reactive({}),
    };
  },

  created() {
    this.posts.data.forEach(post => {
      post.commentContent = '';
      post.showComments = false;
    });
  },

  methods: {
    navigateToHome() {
      this.$inertia.visit('/');
    },
    performSearch() {
      if (!this.searchQuery.trim()) {
        this.visiblePosts = this.posts.data;
        return;
      }

      this.isLoading = true;

      axios
        .get('/posts/search', {
          params: {
            query: this.searchQuery,
          },
        })
        .then((response) => {
          this.visiblePosts = response.data.data;
          this.currentPage = response.data.current_page;
          this.lastPage = response.data.last_page;
        })
        .catch((error) => {
          console.error('Error performing search:', error);
          Swal.fire({
            title: 'Error',
            text: 'Failed to perform the search. Please try again.',
            icon: 'error',
          });
        })
        .finally(() => {
          this.isLoading = false;
        });
    },

    navigateToLogin() {


      this.$inertia.visit(route("login", { redirect: this.currentUrl }));
    }
    ,
    navigateToTag(tag) {

      this.$inertia.get(route('posts.index', { tag: tag.id }));
    },
    fetchPostsByTag(tagId) {
      this.visiblePosts = [];
      this.isLoading = true;

      this.currentTag = parseInt(tagId)

      const url = route('tags.posts', { tag: tagId });

      axios.get(url)
        .then((response) => {
          this.visiblePosts = response.data.data
          console.log(this.visiblePosts);
          this.currentPage = response.data.current_page;
          this.lastPage = response.data.last_page;
        })
        .catch((error) => {
          console.error('Error performing search:', error);
          Toastify({
            text: 'Fail to get posts of the tag. Please try again.',
            duration: 3000,
            close: true,
            gravity: 'top',
            position: 'right',
            backgroundColor: '#F44336',
          }).showToast();
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    navigateToAuthorPosts(authorId) {
      this.$inertia.visit(route('users.posts', { user: authorId }));
    },
    formatDate(date) {
      const now = new Date();
      const givenDate = new Date(date);
      const diffInSeconds = Math.floor((now - givenDate) / 1000);
      const diffInMinutes = Math.floor(diffInSeconds / 60);
      const diffInHours = Math.floor(diffInMinutes / 60);
      const diffInDays = Math.floor(diffInHours / 24);

      if (diffInSeconds < 60) {
        return "Just now";
      } else if (diffInMinutes < 60) {
        return `${diffInMinutes}m ago`;
      } else if (diffInHours < 24) {
        return `${diffInHours}h ago`;
      } else if (diffInDays === 1) {
        return "Yesterday";
      } else if (diffInDays < 7) {
        return `${diffInDays}d ago`;
      } else {
        return givenDate.toLocaleDateString(undefined, {
          month: "short",
          day: "numeric",
          year: now.getFullYear() === givenDate.getFullYear() ? undefined : "numeric",
        });
      }
    },

    toggleCommentSection(post) {
      post.showComments = !post.showComments;
    },
    async submitComment(post) {
      if (!this.currentUser) {
        localStorage.setItem('redirect_after_login', route('posts.show', { post: post.id }));
        Swal.fire({
          title: 'Login Required',
          text: 'You need to log in to comment. Do you want to log in now?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, log in',
          cancelButtonText: 'Cancel',
        }).then((result) => {
          if (result.isConfirmed) {
            this.navigateToLogin();
          } else if (result.isDenied) {
            localStorage.removeItem('redirect_after_login');
          }
        });
        return;
      }

      try {
        const response = await axios.post(route('comments.store', { post: post.id }), {
          content: post.commentContent,
        });

        if (response.data.comment) {
          post.comments.unshift(response.data.comment);
        }
        post.commentContent = '';
        Toastify({
          text: "Comment submited successfully.",
          duration: 3000,
          close: true,
          gravity: 'top',
          position: 'right',
          backgroundColor: '#4CAF50',
        }).showToast();
      } catch (error) {
        Toastify({
          text: 'Fail to submit the comment. Please try again.',
          duration: 3000,
          close: true,
          gravity: 'top',
          position: 'right',
          backgroundColor: '#F44336',
        }).showToast();
      }
    },
    async deleteComment(comment) {
      try {
        const url = route('comments.destroy', comment.id);
        await axios.delete(url);
        const post = this.posts.data.find(p => p.comments.some(c => c.id === comment.id));
        if (post) {
          post.comments = post.comments.filter(c => c.id !== comment.id);
        }
        Toastify({
          text: "Comment deleted.",
          duration: 3000,
          close: true,
          gravity: 'top',
          position: 'right',
          backgroundColor: '#4CAF50',
        }).showToast();
      } catch (error) {
        Toastify({
          text: 'Failed to delete the comment. Please try again.',
          duration: 3000,
          close: true,
          gravity: 'top',
          position: 'right',
          backgroundColor: '#F44336',
        }).showToast();
      }
    },

    async toggleLike(post) {
      if (!this.$page.props.auth.user) {
        localStorage.setItem('redirect_after_login', route('posts.show', { post: post.id }));

        Swal.fire({
          title: 'Login Required',
          text: 'You need to log in to like a post. Do you want to log in now?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, log in',
          cancelButtonText: 'Cancel',
        }).then((result) => {
          if (result.isConfirmed) {
            this.navigateToLogin();
          } else if (result.isDenied) {
            localStorage.removeItem('redirect_after_login');
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

    sharePost(postId) {
      const postUrl = `${window.location.origin}/posts/${postId}`;

      navigator.clipboard.writeText(postUrl)
        .then(() => {
          Toastify({
            text: 'Post link copied to clipboard!',
            duration: 3000,
            close: true,
            gravity: 'top',
            position: 'right',
            backgroundColor: '#4CAF50',
          }).showToast();
        })
        .catch((error) => {
          console.error('Failed to copy URL:', error);
          Toastify({
            text: 'Failed to copy post link. Please try again.',
            duration: 3000,
            close: true,
            gravity: 'top',
            position: 'right',
            backgroundColor: '#F44336',
          }).showToast();
        });

    },
    createNewPost() {
      if (!this.$page.props.auth.user) {

        localStorage.setItem('redirect_after_login', route('posts.create'));

        // const a = localStorage.getItem('redirect_after_login');
        // console.log('a', a);  // Output: The stored route URL

        Swal.fire({
          title: 'Login Required',
          text: 'You need to log in to create a new post. Do you want to log in now?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, log in',
          cancelButtonText: 'Cancel',
        }).then((result) => {
          if (result.isConfirmed) {
            this.navigateToLogin();
          }
          else if (result.isDenied) {
            localStorage.removeItem('redirect_after_login');
          }
        });
      } else {
        this.$inertia.visit(route('posts.create'));

      }
    },
    editPost(post) {

      if (this.currentUser && (this.currentUser.role === 'admin' || post.user_id === this.currentUser.id)) {
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
    async loadMorePosts() {
      if (this.currentPage >= this.lastPage || this.isLoading) return;

      this.isLoading = true;

      try {
        const nextPage = this.currentPage + 1;
        let url;

        if (this.currentTag) {
          url = route('tags.posts', {
            tag: this.currentTag,
            page: nextPage,
          });
        } else {
          url = `/posts?page=${nextPage}`;
        }

        const response = await axios.get(url);
        console.log('API Response:', response);

        if (Array.isArray(response.data.data)) {
          this.visiblePosts = [...this.visiblePosts, ...response.data.data];
          this.currentPage = nextPage;
          this.lastPage = response.data.last_page;
        }
      } catch (error) {
        console.error('Error loading more posts:', error);
      } finally {
        this.isLoading = false;
      }
    },
    handleScroll() {
      const bottomOfWindow =
        document.documentElement.scrollTop + window.innerHeight >=
        document.documentElement.offsetHeight - 100;
      if (bottomOfWindow) {
        this.loadMorePosts();
      }
    },
    //handle show images modal
    openImageModal(post, index) {
      this.modalImages = post.images.map((image) => `/storage/${image.path}`);
      this.currentImageIndex = index;
      this.currentImage = this.modalImages[index];
      this.isModalOpen = true;
      window.addEventListener("keydown", this.handleKeydown);
    },
    closeImageModal() {
      this.isModalOpen = false;
      this.modalImages = [];
      this.currentImage = "";
      this.currentImageIndex = 0;
      window.removeEventListener("keydown", this.handleKeydown);
    },
    prevImage() {
      if (this.currentImageIndex > 0) {
        this.currentImageIndex -= 1;
        this.currentImage = this.modalImages[this.currentImageIndex];
      }
    },
    nextImage() {
      if (this.currentImageIndex < this.modalImages.length - 1) {
        this.currentImageIndex += 1;
        this.currentImage = this.modalImages[this.currentImageIndex];
      }
    },
    handleKeydown(event) {
      if (this.isModalOpen) {
        switch (event.key) {
          case "ArrowLeft":
            this.prevImage();
            break;
          case "ArrowRight":
            this.nextImage();
            break;
          case "Escape":
            this.closeImageModal();
            break;
          default:
            break;
        }
      }
    },
    // handle showing content of posts

    toggleText(postId) {
      this.expandedPosts[postId] = !this.expandedPosts[postId];
    },
    truncatedText(fullText, postId) {
      if (this.expandedPosts[postId] || fullText.length <= this.maxLength) {
        return fullText;
      } else {
        return fullText.slice(0, this.maxLength) + "...";
      }
    },
    showSeeMoreButton(fullText, postId) {
      return fullText.length > this.maxLength && !this.expandedPosts[postId];
    },
    showSeeLessButton(fullText, postId) {
      return fullText.length > this.maxLength && this.expandedPosts[postId];
    },
    performSearch() {
      if (!this.searchQuery.trim()) {
        this.visiblePosts = this.posts.data;
        return;
      }

      this.isLoading = true;

      axios
        .get('/posts/search', {
          params: {
            query: this.searchQuery,
          },
        })
        .then((response) => {
          this.visiblePosts = response.data.data;
          this.currentPage = response.data.current_page;
          this.lastPage = response.data.last_page;
        })
        .catch((error) => {
          console.error('Error performing search:', error);
          Swal.fire({
            title: 'Error',
            text: 'Failed to perform the search. Please try again.',
            icon: 'error',
          });
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    showNotifications() {
      if (!this.currentUser) {
        localStorage.set('redirect_after_login', route('home'))
        Swal.fire({
          title: 'Login Required',
          text: 'Log in to see your notifications. Do you want to log in now?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, log in',
          cancelButtonText: 'Cancel',
        }).then((result) => {
          if (result.isConfirmed) {
            this.navigateToLogin();
          }
          else if (result.isDenied) {
            localStorage.removeItem('redirect_after_login');
          }
        });
      } else {
        this.toggleNotificationDropdown();
        if (this.isNotificationDropdownOpen) {

          this.fetchNotifications(this.currentUser.id);
          this.markNotificationsAsRead();
        }

      }
    },
    fetchNotifications(userId) {

      axios.get(`/api/notifications?user_id=${userId}&page=${this.page}&per_page=${this.perPage}`)
        .then(response => {
          console.log("API Response:", response.data.data);

          this.notifications = [...this.notifications, ...response.data.data];
          this.visibleNotifications = this.notifications.slice(0, this.page * this.perPage);
          this.unreadNotifications = this.notifications.filter(n => !n.read).length;
        })
        .catch(error => {
          console.error("Error fetching notifications:", error);
          this.notifications = [];
        });
    },
    markNotificationsAsRead() {
      if (!this.currentUser) {
        console.warn("User is not authenticated.");
        return;
      }

      axios.post('/api/notifications/mark-as-read', { user_id: this.currentUser.id })
        .then(() => {
          if (Array.isArray(this.notifications)) {
            this.notifications.forEach(n => n.read = true);
            this.unreadNotifications = 0;
          } else {
            console.warn("Notifications array is not defined.");
          }
        })
        .catch(error => {
          console.error("Error marking notifications as read:", error);
        });
    },
    async toggleNotificationDropdown() {
      // Toggle the dropdown state
      this.isNotificationDropdownOpen = !this.isNotificationDropdownOpen;
      console.log('togglenoti', this.isNotificationDropdownOpen);

      if (this.isNotificationDropdownOpen) {
        await nextTick();
        onClickOutside(
          this.$refs.dropdown,
          (event) => {
            if (this.$refs.bellIcon && !this.$refs.bellIcon.contains(event.target)) {
              this.isNotificationDropdownOpen = false;
            }
          }
        );

      }
    },
    loadMoreNotifications() {
      this.page += 1;
      this.fetchNotifications(this.currentUser.id);
    },
    navigateToPost(postId) {
      this.$inertia.visit(route('posts.show', postId));
    },


  },

  mounted() {
    {
      const redirectUrl = localStorage.getItem('redirect_after_login');
      console.log('mounted', redirectUrl)
      if (redirectUrl) {
        localStorage.removeItem('redirect_after_login');
        this.$inertia.visit(redirectUrl);
      }
    }
    {
      const tagId = this.$page.props.tag;
      if (tagId) {
        this.fetchPostsByTag(tagId);
      }
    }
    if (this.currentUser) {
      console.log(`Listening for notifications on: notifications.${this.currentUser.id}`);

      window.Echo.private(`notifications.${this.currentUser.id}`)
        .listen('.comment.added', (e) => {
          console.log('Received Comment Notification:', e); // Debugging log
          if (e.notification) {
            this.notifications.unshift(e.notification);
            this.unreadNotifications++;
          } else {
            console.warn("Received event, but no notification data.");
          }
        })
        .listen('.post.liked', (e) => {
          console.log('Received Like Notification:', e); // Debugging log
          if (e.notification) {
            this.notifications.unshift(e.notification);
            this.unreadNotifications++;
          } else {
            console.warn("Received event, but no notification data.");
          }
        });
    } else {
      console.warn("Log in to check your notifications.");
    }


    window.addEventListener('scroll', this.handleScroll);

  },

  beforeDestroy() {
    window.removeEventListener('scroll', this.handleScroll);

  },

};

</script>

<style>
/* Navbar Styles */
.navbar {
  min-height: 60px;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 16px;
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
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: none;
  border: none;
  cursor: pointer;
}

.notification-badge {

  top: 10px;
  right: 10px;
  background: red;
  color: white;
  border-radius: 50%;
  padding: 2px 6px;
  font-size: 10px;
  position: absolute;
  background: red;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  transform: translate(50%, -50%);

}

.notification-dropdown {
  position: absolute;
  right: 0;
  top: 100%;
  background-color: white;
  border: 1px solid #ccc;
  border-bottom-left-radius: 8px;
  max-height: 300px;
  overflow-y: auto;
  width: 300px;
  z-index: 1000;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.notification-item {
  padding: 10px;
  border-bottom: 1px solid #eee;
  cursor: pointer;
}

.notification-item:hover {
  background-color: #f9f9f9;
}

.notification-item:last-child {
  border-bottom: none;
}

.see-more-button {
  width: 100%;
  padding: 10px;
  color: rgb(59 130 246 / var(--tw-text-opacity, 1));
  border: none;
  cursor: pointer;
}

.see-more-button:hover {

  text-decoration: underline;
}

.user-section {
  display: flex;
  align-items: center;
  min-width: 40px;
  /* gap: 8px; */
}

.user-name {
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

/* create post */
.post-list {
  max-width: 550px;

  margin: 0 auto;
}

.status-box {
  position: relative;
  display: flex;
  align-items: center;
  background: #fff;
  border-radius: 8px;
  padding: 10px 15px;
  width: 100%;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.status-input {

  flex: 1;
  border: none;
  outline: none;
  font-size: 16px;
  background: #f0f2f5;
  padding: 10px 15px;
  border-radius: 8px;
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
  flex-shrink: 0;
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
  max-width: 460px;
}

.post-images {
  display: grid;
  gap: 8px;
  margin-bottom: 12px;
}

.image {
  width: auto;

  border-radius: 8px;
  object-fit: cover;
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
  background: rgba(0, 0, 0, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-image {
  max-width: 80%;
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
  font-size: 5em;
  cursor: pointer;
  transform: translateY(-50%);
}

.prev {
  left: 3%;
}

.next {
  right: 3%;
}
</style>
