<template>
  <div>
    <h1>Edit Post</h1>
    <form @submit.prevent="submit">
      <!-- Title Field -->
      <div>
        <label for="title">Title</label>
        <input v-model="form.title" id="title" type="text" />
        <span v-if="errors.title">{{ errors.title }}</span>
      </div>

      <!-- Content Field -->
      <div>
        <label for="content">Content</label>
        <textarea v-model="form.content" id="content"></textarea>
        <span v-if="errors.content">{{ errors.content }}</span>
      </div>

      <!-- Tags Field -->
      <div>
        <label for="tags">Tags</label>
        <!-- Input to add new tags -->
        <input v-model="newTag" id="tags" type="text" placeholder="Add a tag" @keyup.enter="addTag" />
        <!-- List of existing tags (can be removed) -->
        <div>
          <h4>Existing Tags</h4>
          <span v-if="form.tags.length === 0">No existing tags.</span>
          <span v-for="(tag, index) in form.tags" :key="index" class="tag">
            {{ tag }}
            <button type="button" @click="removeTag(index)">×</button>
          </span>
        </div>
        <!-- List of available tags (can be added) -->
        <div>
          <h4>Available Tags</h4>
          <span v-if="availableTags.length === 0">No available tags.</span>
          <span v-for="(tag, index) in availableTags" :key="index" class="tag">
            {{ tag }}
            <button type="button" @click="addExistingTag(index)">+</button>
          </span>
        </div>
        <span v-if="errors.tags">{{ errors.tags }}</span>
      </div>

      <!-- Submit Button -->
      <button type="submit">Update</button>
    </form>
  </div>
</template>

<script>
import { useForm, usePage } from '@inertiajs/vue3';
import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';
import { ref, computed, onMounted } from 'vue';

export default {
  props: {
    post: {
      type: Object,
      required: true,
    },
    errors: {
      type: Object,
      default: () => ({}),
    },
    allTags: {
      type: Array,
      default: () => [],
    },

  },
  setup(props) {
    onMounted(() => {
      console.log('Post:', props.post);
      console.log('All Tags:', props.allTags);
    });

    const form = useForm({
      title: props.post.title,
      content: props.post.content,
      tags: props.post.tags ? props.post.tags.map((tag) => tag.name) : [],
    });

    const newTag = ref('');

    const availableTags = computed(() => {
      return props.allTags
        .map((tag) => tag.name)
        .filter((tag) => !form.tags.includes(tag));
    });

    const addTag = () => {
      if (newTag.value.trim() !== '' && !form.tags.includes(newTag.value.trim())) {
        form.tags.push(newTag.value.trim());
        newTag.value = '';
      }
    };

    const addExistingTag = (index) => {
      const tag = availableTags.value[index];
      if (!form.tags.includes(tag)) {
        form.tags.push(tag);
      }
    };

    const removeTag = (index) => {
      form.tags.splice(index, 1);
    };

    const submit = () => {
      form.put(`/posts/${props.post.id}`);
      Toastify({
        text: "post updated successfully",
        duration: 3000,
        close: true,
        gravity: 'top',
        position: 'right',
        backgroundColor: '#4CAF50',
      }).showToast();
    };

    return { form, newTag, availableTags, addTag, addExistingTag, removeTag, submit };
  },
};
</script>

<style>
.tag {
  display: inline-block;
  background-color: #f0f0f0;
  padding: 4px 8px;
  border-radius: 4px;
  margin-right: 8px;
  margin-bottom: 8px;
}

.tag button {
  background: none;
  border: none;
  cursor: pointer;
  color: #888;
  margin-left: 4px;
}

.tag button:hover {
  color: #333;
}
</style>