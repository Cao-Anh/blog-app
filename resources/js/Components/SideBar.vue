<template>
    <div class="sidebar">
        <h3>Tags</h3>
        <div class="tags">
          <span v-for="tag in allTags" :key="tag.id" @click="navigateToTag(tag)" class="tag">{{ tag.name }}</span>
        </div>
      </div>
</template>
<script>
import { router } from '@inertiajs/vue3';

export default {
  props: {
    allTags: {
      type: Array,
      required: true,
    },
  },
  methods: {
    navigateToTag(tag) {
      this.$inertia.visit(`/tags/${tag.id}`);
    },
  },
};
</script>

<style scoped>
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
</style>