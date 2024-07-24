<template>
    <div class="hidden lg:block p-4 h-screen fixed pb-24 mt-20 overflow-hidden w-20 no-scrollbar transition-[width]
                duration-300 ease-in-out bg-[url('/images/pkt-pattern.png')] bg-contain bg-left-bottom bg-no-repeat bg-primary-surface z-50"
        :class="[
            { 'w-20': sidemenu },
            { 'w-64': !sidemenu }
        ]">
        <nav class="flex-grow overflow-auto no-scrollbar" :class="[
            { 'h-[60vh]': fixedMenuItems.length },
            { 'h-screen pb-24': !fixedMenuItems.length }
        ]">
            <ul class="my-2 flex flex-col gap-2 items-stretch">
                <li v-for="menuItem, index in sideMenuItems" :key="index">
                    <div>
                        <div v-if="can(menuItem.permission) && menuItem.type === 'header' && !sidemenu" class="text-xs text-gray-700 uppercase font-bold mb-2  transition-[width] overflow-hidden overflow-elipsis whitespace-nowrap">
                            {{ menuItem.label }}
                        </div>
                        <div v-if="can(menuItem.permission) && menuItem.type === 'header' && sidemenu" class="mb-2 pl-2">
                            <hr class=" border-t-[3px] border-gray-400 mb-4 mt-2">
                        </div>
                    </div>
                    <sidebar-menu-item v-if="can(menuItem.permission) && menuItem.type !== 'header'" :menu-item="menuItem" :key="index">
                        {{ menuItem.permission }}
                    </sidebar-menu-item>
                </li>
            </ul>
        </nav>

        <div v-if="fixedMenuItems.length">
            <nav class="flex-grow max-h-[30vh] overflow-auto no-scrollbar border-t-2 border-gray-300">
                <ul class="my-2 flex flex-col gap-2 items-stretch">
                    <li v-for="menuItem, index in fixedMenuItems" :key="index">
                        <div v-if="can(menuItem.permission) && menuItem.type === 'fixed-header' && !sidemenu" class="text-xs text-gray-700 uppercase font-bold mb-2">
                            {{ menuItem.label }}
                        </div>
                        <sidebar-menu-item v-if="can(menuItem.permission) && menuItem.type !== 'fixed-header'" :menu-item="menuItem" :key="index">
                            {{ menuItem.permission }}
                        </sidebar-menu-item>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div class="block lg:hidden p-4 fixed h-screen mt-20 overflow-hidden w-64 no-scrollbar transition-all shadow-xl
                duration-300 ease-in-out bg-[url('/images/pkt-pattern.png')] bg-contain bg-left-bottom bg-no-repeat bg-primary-surface z-50"
        :class="[
            { '-left-64': !drawer },
            { 'left-0': drawer }
        ]">
        <nav class="flex-grow overflow-auto no-scrollbar" :class="[
            { 'h-[60vh]': fixedMenuItems.length },
            { 'h-screen pb-24': !fixedMenuItems.length }
        ]">
            <ul class="my-2 flex flex-col gap-2 items-stretch">
                <li v-for="menuItem, index in sideMenuItems" :key="index">
                    <div v-if="can(menuItem.permission) && menuItem.type === 'header' && !sidemenu" class="text-xs text-gray-700 uppercase font-bold mb-2">
                        {{ menuItem.label }}
                    </div>
                    <sidebar-menu-item v-if="can(menuItem.permission) && menuItem.type !== 'header'" :menu-item="menuItem" :key="index">
                        {{ menuItem.permission }}
                    </sidebar-menu-item>
                </li>
            </ul>
        </nav>
        <div v-if="fixedMenuItems.length">
            <nav class="flex-grow max-h-[30vh] overflow-auto no-scrollbar border-t-2 border-gray-300">
                <ul class="my-2 flex flex-col gap-2 items-stretch">
                    <li v-for="menuItem, index in fixedMenuItems" :key="index">
                        <div v-if="can(menuItem.permission) && menuItem.type === 'fixed-header' && !sidemenu" class="text-xs text-gray-700 uppercase font-bold mb-2">
                            {{ menuItem.label }}
                        </div>
                        <sidebar-menu-item v-if="can(menuItem.permission) && menuItem.type !== 'fixed-header'" :menu-item="menuItem" :key="index">
                            {{ menuItem.permission }}
                        </sidebar-menu-item>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
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

const sideMenuItems = ref(navItems.filter(item => !item.type?.includes('fixed')));
const fixedMenuItems = ref(navItems.filter(item => item.type?.includes('fixed')));
</script>
