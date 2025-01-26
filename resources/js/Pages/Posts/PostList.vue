<template>
    <div class="post-list">
        <h1>Posts</h1>
        <button @click="createNewPost" class="btn">Create New Post</button>
        <PostItem v-for="post in posts" :key="post.id" :post="post" />
    </div>
</template>

<script>
import { router } from '@inertiajs/vue3';

import PostItem from '@/Components/PostItem.vue';
import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';
import Swal from 'sweetalert2';

export default {
    components: {
        PostItem,
    },
    props: {
        posts: {
            type: Array,
            required: true,
        },

    },
    created() {
        this.posts.forEach(post => {
            post.commentContent = '';
            post.showComments = false; // Add a flag to toggle comment section
        });
    },
    methods: {
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
    },
};
</script>

<style scoped>
.post-list {
    max-width: 600px;
    margin: 0 auto;
}
</style>