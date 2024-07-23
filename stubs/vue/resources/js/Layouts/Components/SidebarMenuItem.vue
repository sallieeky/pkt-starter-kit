<template>
    <div :key="unique" class="flex flex-col transition-colors duration-300 cursor-pointer pl-4" :class="[
        { 'rounded-md': !sidemenu },
        { 'rounded-full w-10': sidemenu },
    ]" @click.stop="() => expanded = !expanded">
        <Link 
            :href="menuItem.href" 
            class="group pb-4"
            v-if="!hasSubmenu"
            @click="menuItemClicked"
        >
            <div class="flex justify-between">
                <div class="flex gap-2 align-middle items-center">
                    <div v-if="menuItem.icon != null">
                        <bs-icon 
                            :icon="menuItem.icon" 
                            class="group-hover:text-primary mr-1"
                            :class="[
                                { 'text-primary': isActiveSubmenu },
                                { 'text-gray-900': !isActiveSubmenu },
                            ]"
                        ></bs-icon>
                    </div>
                    <div v-else>
                        <div class="h-1 w-1 rounded-full bg-gray-900 mr-1 group-hover:bg-primary"></div>
                    </div>
                    <p 
                        v-if="!sidemenu || drawer" 
                        class="group-hover:text-primary font-bold text-sm line-clamp-1"
                        :class="[
                            { 'text-primary': isActiveSubmenu },
                            { 'text-gray-900': !isActiveSubmenu },
                        ]"
                    >
                        {{ menuItem.label }}
                    </p>
                </div>
            </div>
        </Link>
        <div v-else class="group">
            <div class="flex justify-between pb-4" @click="sideMenuStore.showSidemenu">
                <div class="flex gap-2 align-middle items-center">
                    <div v-if="menuItem.icon != null">
                        <bs-icon 
                            :icon="menuItem.icon" 
                            class="group-hover:text-primary mr-1"
                            :class="[
                                { 'text-primary': isActiveSubmenu || expanded},
                                { 'text-gray-900': !isActiveSubmenu },
                            ]"
                        ></bs-icon>
                    </div>
                    <div v-else>
                        <div class="h-1 w-1 rounded-full bg-gray-900 mr-1 group-hover:bg-primary"></div>
                    </div>
                    <span 
                        v-if="!sidemenu || drawer" 
                        class="group-hover:text-primary font-bold text-sm line-clamp-1"
                        :class="[
                            { 'text-primary': isActiveSubmenu || expanded},
                            { 'text-gray-900': !isActiveSubmenu },
                        ]"
                    >
                        {{ menuItem.label }}
                    </span>
                </div>
                <bs-icon class="group-hover:text-primary transition-transform duration-300 " :class="[
                    { 'rotate-90 text-primary': expanded },
                    { 'rotate-0 text-gray-800': !expanded },
                ]" icon="chevron-right" v-if="!sidemenu || drawer">
                </bs-icon>
            </div>
        </div>
        <div v-if="hasSubmenu && (!sidemenu || drawer)" class="ml-1 transition-[max-height] duration-500 overflow-hidden ease-in-out" :class="[
            { 'max-h-0': !expanded },
            { 'max-h-[2048px]': expanded },
        ]">
            <ul>
                <div v-for="childMenuItem, index in menuItem.submenu" :key="index">
                    <sidebar-menu-item 
                        v-if="can(childMenuItem.permission)"
                        :menu-item="childMenuItem" 
                        :key="index"
                        :is-submenu="true"
                        @expand="onChildExpand"
                    >
                    </sidebar-menu-item>
                </div>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import BsIcon from '@/Components/BsIcon.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { can } from '@/Core/Helpers/permission-check';
import { useSidemenuStore } from '@/Stores/sidemenu';

const emit = defineEmits(['expand']);

const props = defineProps({
    unique: Number,
    menuItem: Object,
    isSubmenu: {
        type: Boolean,
        default: false,
    },
    expand: {
        type: Boolean,
        default: false,
    }
})

const sideMenuStore = useSidemenuStore();

const sidemenu = computed(()=>sideMenuStore.sidemenu);
const drawer = computed(()=>sideMenuStore.drawer);

const expanded = ref(props.expand);
const isActiveSubmenu = ref(usePage().url == props.menuItem.href || usePage().url.startsWith(props.menuItem.href + '/'));

const hasSubmenu = computed(() => {
    return props.menuItem.submenu != null && props.menuItem.submenu.length > 0;
});
onMounted(()=>{
    if(usePage().url == props.menuItem.href || usePage().url.startsWith(props.menuItem.href + '/')) emit("expand");
})
const onChildExpand = ()=>{
    expanded.value = true;
}
function menuItemClicked(){
    sideMenuStore.hideDrawer();
    sideMenuStore.showSidemenu();
}
</script>