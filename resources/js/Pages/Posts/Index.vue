<template>
  <div>
    <h1>Posts</h1>
    <button @click="handleCreateNewPost" class="btn">Create New Post</button>

    <table>
      <thead>
        <tr>
          <th>Title</th>
          <th>Author</th>
          <th v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="post in posts" :key="post.id">
          <td>{{ post.title }}</td>
          <td>{{ post.author.name }}
            <button @click="toggleLike(post)" :class="{ 'liked': post.is_liked }">
              Like ({{ post.likes_count }})
            </button>
            <form @submit.prevent="submitComment(post)">
              <input type="hidden" name="redirect" :value="currentUrl" />
              <textarea v-model="post.commentContent" placeholder="Add a comment"></textarea>
              <button type="submit">Submit</button>
            </form>

            <!-- Display Comments -->
            <div v-if="post.comments && post.comments.length > 0">
              <div v-for="comment in post.comments" :key="comment.id">
                <p>{{ comment.content }}</p>
                <small>By {{ comment.user.name }}</small>
                <button
                  v-if="$page.props.auth.user && ($page.props.auth.user.id === comment.user_id || $page.props.auth.user.role === 'admin')"
                  @click="deleteComment(comment)">
                  Delete
                </button>
              </div>
            </div>
            <div v-else>
              <p>No comments yet.</p>
            </div>
          </td>
          <td>
            <div v-if="post.tags.length > 0">
              <span v-for="tag in post.tags" :key="tag.id" class="tag">
                {{ tag.name }}
              </span>
            </div>
          </td>
          <td>
            <button v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'"
              @click="editPost(post)">Edit</button>
            <button v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'"
              @click="deletePost(post.id)">Delete</button>
          </td>

        </tr>
      </tbody>
    </table>
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
  },
  data() {
    return {
      currentUrl: '',
    };
  },
  created() {
    this.posts.forEach(post => {
      post.commentContent = '';
    })
  },
  methods: {
    // Edit post method
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
          backgroundColor: '#F44336', // Red for error
        }).showToast();
      }
    },

    // Delete post method
    deletePost(id) {
      if (confirm('Are you sure you want to delete this post?')) {
        this.$inertia.delete(`/posts/${id}`, {
          onFinish: () => {
            if (this.$page.props.flash.success) {
              Toastify({
                text: this.$page.props.flash.success,
                duration: 3000,
                close: true,
                gravity: 'top',
                position: 'right',
                backgroundColor: '#4CAF50', // Green for success
              }).showToast();
            }
            if (this.$page.props.flash.error) {
              Toastify({
                text: this.$page.props.flash.error,
                duration: 3000,
                close: true,
                gravity: 'top',
                position: 'right',
                backgroundColor: '#F44336', // Red for error
              }).showToast();
            }
          },
        });
      }
    },
    handleCreateNewPost() {
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
        const response = await axios.post(route('posts.like', post.id));

        console.log('Like toggled successfully:', response.data);
      } catch (error) {
        post.is_liked = wasLiked;
        post.likes_count += post.is_liked ? 1 : -1;
        console.error('Error toggling like:', error);
      }
    },

    mounted() {
      this.currentUrl = window.location.href;
      console.log('Posts data:', this.posts);
      // if (this.$page.props.flash.success) {
      //   Toastify({
      //     text: this.$page.props.flash.success,
      //     duration: 3000,
      //     close: true,
      //     gravity: 'top',
      //     position: 'right',
      //     backgroundColor: '#4CAF50', // Green for success
      //   }).showToast();
      // }
    },
  },
};
</script>

<style>
.liked {
  color: red;
  font-weight: bold;
}
.tag {
  background-color: #f0f0f0;
  padding: 4px 8px;
  border-radius: 4px;
  margin-right: 8px;
}
</style>