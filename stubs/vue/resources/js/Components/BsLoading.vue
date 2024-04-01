<template>
    <div class="flex flex-col items-center justify-center">
        <img src="/images/loading.svg" :height="size" :width="size"/>
        <span v-if="text" class="font-bold italic">{{text}}</span>
    </div>
</template>
<script setup>
const props = defineProps({
    size:{
        type: Number,
        default: 100
    },
    text:{
        type: String,
        // default: "Loading ..."
    }
})
</script>