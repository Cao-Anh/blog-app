<template>
   <div class="sidebar">
        <h3>Tags</h3>
        <div class="tags">
          <span v-for="tag in allTags" :key="tag.id" @click="navigateToTag(tag)" class="tag">{{ tag.name }}</span>
        </div>
      </div>
</template>
<script>
export default {
  props: {
    allTags: Array,
    visiblePosts: {
      type: Array,
      required: true,
      default: () => []
    },
    isLoading: {
      type: Boolean,
      default: false
    },
    currentTag: {
      type: String,
      default: '' 
    }
  },
  methods: {
    navigateToTag(selectedTag) {
      this.$emit('tag-click', selectedTag);
    }
  }
}
</script>