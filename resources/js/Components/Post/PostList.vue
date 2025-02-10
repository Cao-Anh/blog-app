<template>
     <div class="post-list">
        <h1>Posts</h1>
        <button @click="createNewPost" class="btn">Create New Post</button>

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
            <p class="font-bold m">{{ post.title }}</p>
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
          <div v-if="post.tags.length > 0" class="tags">
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
        <div v-if="isLoading" class="loading">Loading more posts...</div>
        <div v-if="currentPage >= lastPage" class="no-more-posts">No more posts to load.</div>
      </div>
</template>

<script>


export default {
    components: {
        
    },
    props: {
        posts: {
            type: Object,
            required: true,
        },

    },
    created() {
        
    },
    methods: {
        
    },
};
</script>

