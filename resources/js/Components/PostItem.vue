<template>
    <div class="post">
        <!-- Admin Actions -->
        <div v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'" class="admin-actions">
            <button @click="editPost(post)" class="icon-button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16">
                    <path
                        d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a.996.996 0 0 0 0-1.41l-2.34-2.34a.996.996 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                </svg>
            </button>
            <button @click="deletePost(post.id)" class="icon-button">
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
        <CommentSection v-if="post.showComments" :post="post" />
    </div>
</template>

<script>
import { router } from '@inertiajs/vue3';

import CommentSection from './CommentSection.vue';
import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';

export default {
    components: {
        CommentSection,
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
};
</script>

<style scoped>
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
</style>