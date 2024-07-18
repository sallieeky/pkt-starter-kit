<template>
    <button class=" hover:bg-gray-500 hover:bg-opacity-20 p-2 rounded-full" aria-label="icon-btn">
        <BsIcon :icon="icon"/>
    </button>
</template>
<script setup>
import BsIcon from '@/Components/BsIcon.vue';

const props = defineProps({
    type:{
        type: String,
        default:"primary"
    },
    icon:{
        type: String,
        required: true
    }
});
</script>