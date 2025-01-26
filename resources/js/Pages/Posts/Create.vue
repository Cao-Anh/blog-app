<template>
  <div>
    <h1>Create New Post</h1>
    <form @submit.prevent="submit">
      <div>
        <label for="title">Title</label>
        <input id="title" v-model="form.title" type="text" />
        <span v-if="errors.title">{{ errors.title }}</span>
      </div>
      <div>
        <label for="content">Content</label>
        <textarea id="content" v-model="form.content"></textarea>
        <span v-if="errors.content">{{ errors.content }}</span>
      </div>
      <button type="submit">Create</button>
    </form>
  </div>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';

export default {
  props: {
    errors: {
      type: Object,
      default: () => ({}),
    },
  },
  setup(props) {


    const form = useForm({
      title: '',
      content: '',
    });

    const submit = () => {
      form.post('/posts');
    };

    return { form, submit };
  },
};
</script>