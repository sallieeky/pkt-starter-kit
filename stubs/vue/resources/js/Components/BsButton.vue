<template>
    <button 
        class="align-middle font-bold m-1" 
        :class="style"
    >
        <div class="flex items-center justify-center align-middle gap-1">
            <div v-if="icon != null">
                <bs-icon :icon="icon" :size="iconSize"></bs-icon>
            </div>
            <div class="h-5 flex items-center justify-center align-middle" v-if="props.showLabel">
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
        default: "medium"
    },
    icon:{
        type: String
    },
    showLabel:{
        type: Boolean,
        default: true
    }
});

// Button Style
const style = ref([]);
const iconSize = ref(24);

switch(props.type){
    case 'primary':
        style.value = [
            ...style.value,
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
            ...style.value,
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
            ...style.value,
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
            ...style.value,
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
            ...style.value,
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
            ...style.value,
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
        style.value = [
            ...style.value,
            "bg-primary",
            "text-white",
            "hover:bg-primary-hover",
            "focus:bg-primary-hover",
            "active:bg-primary-pressed",
        ]
        break;
}

switch(props.size){
    case 'small':
        style.value = [
            ...style.value,
            "px-2",
            "py-2",
            "text-[11px]",
            "rounded-md",
        ];
        iconSize.value = 18;
        break;
    case 'medium':
        style.value = [
            ...style.value,
            "px-3",
            "py-2",
            "text-[12px]",
            "rounded-lg",
        ];
        iconSize.value = 22;
        break;
    case 'large':
        style.value = [
            ...style.value,
            "px-4",
            "py-3",
            "text-[13px]",
            "rounded-lg",
        ];
        iconSize.value = 24;
        break;
    default:
        style.value = [
            ...style.value,
            "px-3",
            "py-2",
            "text-[12px]",
            "rounded-lg",
        ];
        iconSize.value = 22;
        break;
}
</script>