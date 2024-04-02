<template>
    <button 
        class="px-3 py-2 text-[12px] align-middle rounded-lg font-bold m-1" 
        :class="style"
    >
        <div class="flex items-center">
            <div class="pr-2" v-if="icon != null">
                <bs-icon :icon="icon"></bs-icon>
            </div>
            <div class="h-5 flex items-center">
                <slot/>
            </div>
        </div>
    </button>
</template>

<script setup>
import { ref, onMounted } from "vue";
import BsIcon from "@/Components/BsIcon.vue";

const props = defineProps({
    type:{
        type: String,
        default:"primary"
    },
    disabled:{
        type: Boolean,
        default: false
    },
    size:{
        type: String,
        default: "small"
    },
    icon:{
        type: String
    }
});

// Button Style
const style = ref([]);
switch(props.type){
    case 'primary':
        style.value = [
            "bg-primary",
            "text-white",
            "hover:bg-primary-hover",
            "focus:bg-primary-hover",
            "active:bg-primary-pressed",
        ]
        break;
    case 'primary-outline':
        style.value = [
            "bg-white",
            "text-primary",
            "border border-primary",
            "hover:bg-primary-focus",
            "hover:border-primary-hover",
            "focus:bg-primary-focus",
            "focus:border-primary",
            "active:bg-primary-border",
            "active:border-primary-pressed",
        ]
        break;
    case 'secondary':
        style.value = [
            "bg-secondary",
            "text-white",
            "hover:bg-secondary-hover",
            "focus:bg-secondary-hover",
            "active:bg-secondary-pressed",
        ]
        break;
    case 'secondary-outline':
        style.value = [
        "bg-white",
            "text-secondary",
            "border border-secondary",
            "hover:bg-secondary-focus",
            "hover:border-secondary-hover",
            "focus:bg-secondary-focus",
            "focus:border-secondary",
            "active:bg-secondary-border",
            "active:border-secondary-pressed",
        ]
        break;
    case 'danger':
        style.value = [
            "bg-danger",
            "text-white",
            "hover:bg-danger-hover",
            "focus:bg-danger-hover",
            "active:bg-danger-pressed",
        ]
        break;
    case 'danger-outline':
        style.value = [
        "bg-white",
            "text-danger",
            "border border-danger",
            "hover:bg-danger-focus",
            "hover:border-danger-hover",
            "focus:bg-danger-focus",
            "focus:border-danger",
            "active:bg-danger-border",
            "active:border-danger-pressed",
        ]
        break;
    default:
        break;
}
</script>