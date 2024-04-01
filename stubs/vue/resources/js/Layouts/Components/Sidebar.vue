<template>
    <div class="hidden lg:block p-4 fixed h-screen max-h-screen mt-20 overflow-scroll w-20 no-scrollbar transition-[width] 
                duration-300 ease-in-out bg-[url('/images/pkt-pattern.png')] bg-contain bg-left-bottom bg-no-repeat bg-primary-surface z-50"
        :class="[
            { 'w-20': sidemenu },
            { 'w-64': !sidemenu }
        ]">
        <nav class="flex-grow">
            <ul class="my-2 flex flex-col gap-2 items-stretch">
                <li v-for="menuItem, index in sideMenuItems" :key="index">
                    <sidebar-menu-item v-if="can(menuItem.permission)" :menu-item="menuItem" :key="index">
                        {{ menuItem.permission }}
                    </sidebar-menu-item>
                </li>
            </ul>
        </nav>
    </div>
    <div class="block lg:hidden p-4 fixed h-screen max-h-screen mt-20 overflow-scroll w-64 no-scrollbar transition-all shadow-xl
                duration-300 ease-in-out bg-[url('/images/pkt-pattern.png')] bg-contain bg-left-bottom bg-no-repeat bg-primary-surface z-50"
        :class="[
            { '-left-64': !drawer },
            { 'left-0': drawer }
        ]">
        <nav class="flex-grow">
            <ul class="my-2 flex flex-col gap-2 items-stretch">
                <li v-for="menuItem, index in sideMenuItems" :key="index">
                    <sidebar-menu-item v-if="can(menuItem.permission)" :menu-item="menuItem" :key="index">
                        {{ menuItem.permission }}
                    </sidebar-menu-item>
                </li>
            </ul>
        </nav>
    </div>
    <div class="block lg:hidden bg-black"></div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { navItems } from '@/Core/Config/SidemenuItem';
import SidebarMenuItem from './SidebarMenuItem.vue';
import { can } from '@/Core/Helpers/permission-check';
import { useSidemenuStore } from '@/Stores/sidemenu';

const sideMenuStore = useSidemenuStore();

const sidemenu = computed(()=>sideMenuStore.sidemenu);
const drawer = computed(()=>sideMenuStore.drawer);

const sideMenuItems = ref(navItems);
</script>