<template>
    <div class="comment-section">
        <form @submit.prevent="submitComment(post)">
            <textarea v-model="post.commentContent" placeholder="Add a comment"></textarea>
            <button type="submit">Submit</button>
        </form>

        <!-- Display Comments -->
        <div v-if="post.comments.length > 0" class="comments">
            <div v-for="comment in post.comments" :key="comment.id" class="comment">
                <img :src="`/storage/${post.author.profile_picture}`" alt="Profile Picture" class="profile-picture" />
                <div class="comment-content">
                    <span class="comment-author">{{ comment.user.name }}</span>
                    <p>{{ comment.content }}</p>
                    <small>{{ formatDate(comment.created_at) }}</small>
                </div>
                <button
                    v-if="$page.props.auth.user && ($page.props.auth.user.id === comment.user_id || $page.props.auth.user.role === 'admin')"
                    @click="deleteComment(comment)" class="delete-comment">
                    Delete
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { router } from '@inertiajs/vue3';

import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';
import Swal from 'sweetalert2';
import axios from 'axios';
export default {
    components: {
      
    },
    props: {
        post: {
            type: Object,
            required: true,
        },
    },
    methods: {
        formatDate(date) {
            return new Date(date).toLocaleString();
        },
        toggleCommentSection(post) {
            post.showComments = !post.showComments;
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
                        this.$inertia.visit(route('login'));
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
            Swal.fire({
                title: 'Warning!',
                text: 'Are you sure to delete the post?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(`/posts/${id}`);
                    Toastify({
                        text: "Post deleted successfully.",
                        duration: 3000,
                        close: true,
                        gravity: 'top',
                        position: 'right',
                        backgroundColor: '#4CAF50',
                    }).showToast();
                }
            });
        },
    },
};
</script>

<style scoped>
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

.delete-comment {
    background: none;
    border: none;
    color: #f44336;
    cursor: pointer;
}

.admin-actions {
    position: absolute;
    top: 16px;
    right: 16px;
    display: flex;
    gap: 8px;
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