
<template>
  <div>
    <h1>Edit Post</h1>
    <form @submit.prevent="submit">
      <div>
        <label for="title">Title</label>
        <input v-model="form.title" id="title" type="text" />
        <span v-if="errors.title">{{ errors.title }}</span>
      </div>
      <div>
        <label for="content">Content</label>
        <textarea v-model="form.content" id="content"></textarea>
        <span v-if="errors.content">{{ errors.content }}</span>
      </div>
      <button type="submit">Update</button>
    </form>
  </div>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';
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
  },
  setup(props) {
    
    if (!props.post) {
      console.error('Post prop is undefined');
      return;
    }

    // Initialize the form with the post data
    const form = useForm({
      title: props.post.title,
      content: props.post.content,
    });

    // Handle form submission
    const submit = () => {
      form.put(`/posts/${props.post.id}`);
      Toastify({
                text: this.$page.props.flash.success,
                duration: 3000,
                close: true,
                gravity: 'top',
                position: 'right',
                backgroundColor: '#4CAF50', // Green for success
              }).showToast();
    };

    return { form, submit };
  },
};
</script>