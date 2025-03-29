<template>
  <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-semibold mb-4">Edit Post</h1>
    <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-4">
      <!-- Hidden input for redirect URL -->
      <input type="hidden" name="redirect_url" :value="redirectUrl" />
      <!-- Title Field -->
      <div>
        <label for="title" class="block font-medium text-gray-700">Title</label>
        <input v-model="form.title" id="title" type="text"
          class="w-full border rounded-lg p-2 mt-1 focus:ring focus:ring-blue-300" />
        <span v-if="errors.title" class="text-red-500 text-sm">{{ errors.title }}</span>
      </div>

      <!-- Content Field -->
      <div>
        <label for="content" class="block font-medium text-gray-700">Content</label>
        <textarea v-model="form.content" id="content"
          class="w-full border rounded-lg p-2 mt-1 focus:ring focus:ring-blue-300 h-36"></textarea>
        <span v-if="errors.content" class="text-red-500 text-sm">{{ errors.content }}</span>
      </div>

      <!-- Existing Images -->
      <div>
        <label class="block font-medium text-gray-700">Existing Images</label>
        <div v-if="form.images.length > 0 || form.newImages.length > 0" class="flex flex-wrap gap-2">
          <div v-for="(image, index) in form.images" :key="image.id" class="relative">
            <img :src="`/storage/${image.path}`" alt="Existing Image"
              class="w-24 h-24 object-cover rounded-lg border" />
            <button type="button" @click="removeExistingImage(index)"
              class="absolute top-0 right-0 bg-red-500 text-white w-5 h-5 rounded-full flex items-center justify-center">×</button>
          </div>
        </div>
        <div v-else class="text-gray-500">No existing images.</div>
      </div>

      <!-- Add New Images -->
      <div>
        <label for="newImages" class="block font-medium text-gray-700">Add New Images</label>
        <input type="file" id="newImages" multiple @change="handleImageChange"
          class="w-full border rounded-lg p-2 mt-1" />
        <span v-if="errors.newImages" class="text-red-500 text-sm">{{ errors.newImages }}</span>
      </div>

      <!-- Tags Field -->
      <div>
        <label for="tags" class="block font-medium text-gray-700">Tags</label>
        <input v-model="newTag" id="tags" type="text" placeholder="Add a tag" @keyup.enter="addTag"
          class="w-full border rounded-lg p-2 mt-1" />
        <div class="mt-2">
          <h4 class="font-medium text-gray-700">Existing Tags</h4>
          <div class="flex flex-wrap gap-2 mt-1">
            <span v-if="form.tags.length === 0" class="text-gray-500">No existing tags.</span>
            <span v-for="(tag, index) in form.tags" :key="index" class="tag">{{ tag }}
              <button type="button" @click="removeTag(index)" class="ml-2 text-red-500">×</button>
            </span>
          </div>
        </div>
        <div class="mt-2">
          <h4 class="font-medium text-gray-700">Available Tags</h4>
          <div class="flex flex-wrap gap-2 mt-1">
            <span v-if="availableTags.length === 0" class="text-gray-500">No available tags.</span>
            <span v-for="(tag, index) in availableTags" :key="index" class="tag">{{ tag }}
              <button type="button" @click="addExistingTag(index)" class="ml-2 text-blue-500">+</button>
            </span>
          </div>
        </div>
        <span v-if="errors.tags" class="text-red-500 text-sm">{{ errors.tags }}</span>
      </div>

      <!-- Submit Button -->
      <div class="flex justify-center ">
        <button type="button" @click="$inertia.visit(redirectUrl)"
          class="mx-3 bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600">Back Home</button>
        <button type="submit" class="mx-3 bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">Update</button>
      </div>
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
      form.redirect_url = window.history.state?.back || document.referrer ; 
      console.log('Redirect URL:', form.redirect_url);
    });
    const form = useForm({
      title: props.post.title,
      content: props.post.content,
      tags: props.post.tags ? props.post.tags.map((tag) => tag.name) : [],
      images: props.post.images || [],
      newImages: [],
      imagesToDelete: [],
      redirect_url: ref(null),
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

    const handleImageChange = (event) => {
      const files = event.target.files;
      for (let i = 0; i < files.length; i++) {
        form.newImages.push(files[i]);
        // form.images.push(files[i]);
      }
      // console.log('newimg',form.newImages)
      // console.log('imgs',form.images)

    };

    const removeExistingImage = (index) => {
      const image = form.images[index];
      form.imagesToDelete.push(image.id); // Track images to delete
      form.images.splice(index, 1); // Remove from displayed images
    };

    const submit = () => {
      form.post(`/posts/${props.post.id}`, {
        onSuccess: () => {

          Toastify({
            text: "Post updated successfully",
            duration: 3000,
            close: true,
            gravity: 'top',
            position: 'right',
            backgroundColor: '#4CAF50',
          }).showToast();
        },
      });

    }
    return {
      form,
      newTag,
      availableTags,
      addTag,
      addExistingTag,
      removeTag,
      handleImageChange,
      removeExistingImage,
      submit,
    };
  },
};
</script>
<style>
/* .image-container {
  position: relative;
  display: inline-block;
  margin-right: 10px;
  margin-bottom: 10px;
}

.image-preview {
  max-width: 100px;
  height: auto;
  border-radius: 4px;
}

.remove-image-button {
  position: absolute;
  top: 0;
  right: 0;
  background: red;
  color: white;
  border: none;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
} */

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