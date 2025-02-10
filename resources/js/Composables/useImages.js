import { ref } from 'vue';

export default function useImages() {
  const isModalOpen = ref(false);
  const modalImages = ref([]);
  const currentImageIndex = ref(0);

  const openImageModal = (post, index) => {
    modalImages.value = post.images.map(img => `/storage/${img.path}`);
    currentImageIndex.value = index;
    isModalOpen.value = true;
  };

  const closeImageModal = () => {
    isModalOpen.value = false;
    modalImages.value = [];
    currentImageIndex.value = 0;
  };

  const prevImage = () => {
    if (currentImageIndex.value > 0) currentImageIndex.value--;
  };

  const nextImage = () => {
    if (currentImageIndex.value < modalImages.value.length - 1) currentImageIndex.value++;
  };

  return {
    isModalOpen,
    modalImages,
    currentImageIndex,
    openImageModal,
    closeImageModal,
    prevImage,
    nextImage
  };
}