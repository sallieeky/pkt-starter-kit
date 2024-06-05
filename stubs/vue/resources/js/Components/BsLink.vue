<template>
    <Link
        :disabled="disabled"
        :href="href"
        class="inline-block px-3 py-2 text-[12px] align-middle rounded-lg font-bold m-1 cursor-pointer"
        :class="style"
    >
        <div class="flex items-center">
            <div class="pr-2" v-if="icon != null">
                <bs-icon :icon="icon"></bs-icon>
            </div>
            <div class="flex items-center h-5">
                <slot/>
            </div>
        </div>
    </Link>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";
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
    },
    href:{
        type: String,
        default: '#'
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
            "disabled:bg-primary-disabled disabled:cursor-not-allowed",
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
            "disabled:text-primary-disabled disabled:border-primary-disabled disabled:cursor-not-allowed",
        ]
        break;
    case 'secondary':
        style.value = [
            "bg-secondary",
            "text-white",
            "hover:bg-secondary-hover",
            "focus:bg-secondary-hover",
            "active:bg-secondary-pressed",
            "disabled:bg-secondary-disabled disabled:cursor-not-allowed",
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
            "disabled:text-secondary-disabled disabled:border-secondary-disabled disabled:cursor-not-allowed",
        ]
        break;
    case 'danger':
        style.value = [
            "bg-danger",
            "text-white",
            "hover:bg-danger-hover",
            "focus:bg-danger-hover",
            "active:bg-danger-pressed",
            "disabled:bg-danger-disabled disabled:cursor-not-allowed",
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
            "disabled:text-danger-disabled disabled:border-danger-disabled disabled:cursor-not-allowed",
        ]
        break;
    default:
        break;
}
</script>