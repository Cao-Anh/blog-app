<template>
    <div class="post">
      <!-- Admin Actions -->
      <div class="admin-actions" v-if="showAdminActions">
        <!-- Edit/Delete buttons -->
      </div>
  
      <!-- Author Section -->
      <div class="author-section">
        <!-- Author info -->
      </div>
  
      <!-- Post Content -->
      <div class="post-content">
        <!-- Content with see more/less -->
      </div>
  
      <!-- Tags -->
      <div class="tags">
        <span 
          v-for="tag in post.tags" 
          :key="tag.id" 
          class="tag"
          @click="$emit('tag-click', tag)"
        >
          {{ tag.name }}
        </span>
      </div>
  
      <!-- Images -->
      <div class="post-images">
        <ImageModal 
          :images="post.images"
          @image-click="openImageModal"
        />
      </div>
  
      <!-- Like/Comment Section -->
      <div class="actions">
        <button @click="$emit('toggle-like')">
          {{ post.is_liked ? 'Liked' : 'Like' }} ({{ post.likes_count }})
        </button>
        <button @click="$emit('toggle-comments')">
          Comment ({{ post.comments.length }})
        </button>
      </div>
  
      <!-- Comment Section -->
      <CommentSection 
        v-if="post.showComments"
        :post="post"
        @submit-comment="$emit('submit-comment', $event)"
        @delete-comment="$emit('delete-comment', $event)"
      />
    </div>
  </template>
  
  <script>
  export default {
    props: {
      post: Object,
      showAdminActions: Boolean
    },
    methods: {
      openImageModal(index) {
        this.$emit('open-image-modal', this.post, index);
      }
    }
  }
  </script>