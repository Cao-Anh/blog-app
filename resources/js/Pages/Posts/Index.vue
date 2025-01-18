<template>
  <div>
    <h1>Posts</h1>
    <Link href="/posts/create" class="btn">Create New Post</Link>
    <!-- <div v-if="$page.props.flash.message" :class="alertClass">
      {{ $page.props.flash.message }}
    </div> -->
    <table>
      <thead>
        <tr>
          <th>Title</th>
          <th>Author</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="post in posts" :key="post.id">
          <td>{{ post.title }}</td>
          <td>{{ post.author.name }}</td>
          <td>
            <button @click="editPost(post)">Edit</button>
            <button @click="deletePost(post.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';
export default {
  components: { Link },
  props: {
    posts: Array,
  },
  computed: {
    alertClass() {
      return {
        'alert': true,
        'alert-success': this.$page.props.flash.type === 'success',
        'alert-error': this.$page.props.flash.type === 'error',
      };
    },
  },
  // methods: {
  //   deletePost(id) {
  //     if (confirm('Are you sure you want to delete this post?')) {
  //       this.$inertia.delete(`/posts/${id}`);
  //     }
  //   },
  // },
  methods: {
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
    editPost(post) {
      if (this.$page.props.flash.success) {
        router.visit(`/posts/${post.id}/edit`);
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
    }
  },
};
</script>
