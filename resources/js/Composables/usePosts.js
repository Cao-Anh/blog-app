import { ref, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';
import { Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';


export default function usePosts(props) {
  const visiblePosts = ref(props.posts.data);
  const isLoading = ref(false);
  const currentPage = ref(props.posts.current_page);
  const lastPage = ref(props.posts.last_page);
  const currentTag = ref(null);
  const expandedPosts = ref(reactive({}));
  const maxLength = 50;

  // Methods
  const performSearch = async (query) => {
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
  };

  const navigateToTag = async (tag) => {
    this.visiblePosts = [];
      this.isLoading = true;
      this.currentTag = tag;

      const url = route('tags.posts', { tag: tag.id });

      axios.get(url)
        .then((response) => {
          this.visiblePosts = response.data.data;
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
  };

  const loadMorePosts = async () => {
    if (this.currentPage >= this.lastPage || this.isLoading) return;

      this.isLoading = true;

      try {
        const nextPage = this.currentPage + 1;
        let url;

        if (this.currentTag) {
          url = route('tags.posts', {
            tag: this.currentTag.id, 
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
  };

  const toggleCommentSection = (post) => {
    post.showComments = !post.showComments;
  };

  const submitComment = async (post) => {
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
        Toastify({
          text: 'Fail to submit the comment. Please try again.',
          duration: 3000,
          close: true,
          gravity: 'top',
          position: 'right',
          backgroundColor: '#F44336',
        }).showToast();
      }
  };

  const deleteComment = async (comment) => {
    try {
        const url = route('comments.destroy', comment.id);
        console.log('Delete URL:', url); // Log the URL

        await axios.delete(url);

        // Find the post containing the comment and remove the comment from its array
        console.log(this.posts);
        const post = this.posts.data.find(p => p.comments.some(c => c.id === comment.id));
        if (post) {
          post.comments = post.comments.filter(c => c.id !== comment.id);
        }
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
  };

  const toggleLike = async (post) => {
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
  };

  const createNewPost = () => {
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
  };

  const editPost = (post) => {
    const currentUser = this.$page.props.auth.user;
      if (currentUser && (currentUser.role === 'admin' || post.user_id === currentUser.id)) {
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
  };

  const deletePost = (id) => {
    if (confirm('Are you sure you want to delete this post?')) {
        this.$inertia.delete(`/posts/${id}`);
      }
  };

  // Text truncation methods
  const toggleText = (postId) => {
    expandedPosts.value[postId] = !expandedPosts.value[postId];
  };

  const truncatedText = (fullText, postId) => {
    if (expandedPosts.value[postId] || fullText.length <= maxLength) {
      return fullText;
    }
    return fullText.slice(0, maxLength) + "...";
  };

  return {
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
    deletePost,
    toggleText,
    truncatedText
  };
}