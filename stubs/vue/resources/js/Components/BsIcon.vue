<template>
    <div :height="size" :width="size">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            preserveAspectRatio="none" v-html="Icon" class="icon-size">
        </svg>
    </div>
</template>

<script setup>
import { heroIcons } from "@/Core/Icons/hero-icons";
import { computed } from "vue";
import { ref } from "vue";

const props = defineProps({
    icon: {
        type: String
    },
    size: {
        type: Number,
        default: 24
    }
});
const iconSize = computed(()=> props.size+'px');

const Icon = ref(null);
Icon.value = heroIcons(props.icon);
</script>

<style scoped>
    .icon-size{
        width: v-bind('iconSize');
        height: v-bind('iconSize');
    }
</style>