<template>
  <Teleport to="body">
    <Transition name="modal" mode="out-in" appear>
      <div class="inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 fixed" v-if="isVisible">
        <div class="absolute top-0 left-0 flex flex-col items-center w-full h-full">
          <div class="p-4 text-white w-full flex-none">
            <div class="grid grid-cols-3 w-full">
              <span class="text-lg font-bold flex gap-2">
                <BsIcon icon="photo" /> {{ props.label ?? 'Image Preview' }}
              </span>
              <div class="flex items-center justify-center">
                <BsIconButton icon="magnifying-glass-minus" @click="zoomOut" />
                <BsIconButton icon="magnifying-glass-plus" @click="zoomIn" />
              </div>
              <div class="flex items-center justify-end">
                <BsIconButton icon="x-mark" @click="closeModal" />
              </div>
            </div>
          </div>
          <div class="overflow-auto no-scrollbar h-full w-full">
            <div ref="imgContainerRef" class="flex-1 w-full h-full p-4 transition-all" role="dialog">
              <img id="img" ref="imgRef" :src="props.src" alt="Image" class="object-contain">
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, defineEmits, computed, onMounted } from 'vue';
import BsIcon from '@/Components/BsIcon.vue';
import BsIconButton from '@/Components/BsIconButton.vue';
import { watch } from 'vue';

const props = defineProps({
  value: {
    type: Boolean,
    required: true
  },
  label: {
    type: String,
    required: false,
  },
  src: {
    type: String,
    required: true,
  },
});

const emits = defineEmits('update:value');

const isVisible = computed(() => props.value);

const scale = ref(1);
const imgRef = ref();
const imgContainerRef = ref();

const containerHeight = ref();
const containerWidth = ref();
const longestContainerSize = computed(() => containerHeight.value > containerWidth.value ? containerHeight.value : containerWidth.value);

function updateContainerSize() {
  containerHeight.value = imgContainerRef.value?.clientHeight;
  containerWidth.value = imgContainerRef.value?.clientWidth;
}
function updateImageSize() {
  var width = containerWidth.value != longestContainerSize.value ? `${containerWidth.value * scale.value}px` : 'auto';
  var height = containerHeight.value != longestContainerSize.value ? `${containerHeight.value * scale.value}px` : 'auto';
  imgContainerRef.value.style.width = width;
  imgContainerRef.value.style.height = height;
  imgContainerRef.value.scrollIntoView({
    behavior: 'auto',
    block: 'center',
    inline: 'center'
  });
}

watch(imgContainerRef, (_) => {
  updateContainerSize();
  updateImageSize();
})

onMounted(() => {
  if (isVisible.value) {
    document.body.style.overflow = 'hidden';
  }
  window.addEventListener('resize', updateContainerSize);
})

watch(isVisible, (newValue) => {
  if (newValue) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = 'auto';
  }
});

function closeModal() {
  emits('update:value', false);
};

function zoomIn() {
  if (scale.value < 2.0) {
    scale.value += 0.1;
    updateImageSize()
  }
};

function zoomOut() {
  if (scale.value > 1.0) {
    scale.value -= 0.1;
    updateImageSize()
  }
}

</script>
<style scoped>
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease-out;
}

#img {
  width: -webkit-fill-available;
  height: -webkit-fill-available;
}

#img {
  user-drag: none;
  -webkit-user-drag: none;

  user-select: none;
  -moz-user-select: none;
  -webkit-user-select: none;
  -ms-user-select: none;
}
</style>