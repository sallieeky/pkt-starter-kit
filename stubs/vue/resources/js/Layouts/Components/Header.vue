<template>
    <div class="top-0 fixed z-50 w-full">
        <div class="bg-primary-surface mx-auto p-2">
            <div class="flex justify-between">
                <div class="flex align-middle items-center m-2">
                    <button class="block lg:hidden mr-4 ml-3 bg-white p-2 rounded-md shadow-md" @click="toggleDrawer" aria-label="menu-btn">
                        <bs-icon icon="bars-3" class=""></bs-icon>
                    </button>
                    <button class="hidden lg:block mr-4 ml-3 bg-white p-2 rounded-md shadow-md" @click="toggleSideMenu" aria-label="menu-btn">
                        <bs-icon icon="bars-3" class=""></bs-icon>
                    </button>
                    <a href="/">
                        <img src="/images/logo-long.png" class="max-h-7 w-auto" name="logo" alt="app-logo"/>
                    </a>
                </div>
                <div class="flex shrink-0 align-middle items-center m-2 relative">
                    <button class="mr-2 p-2" aria-label="notification-btn">
                        <bs-icon icon="bell" class=""></bs-icon>
                    </button>
                    <button class="group text-start">
                        <div class="shrink-0 rounded-lg py-2 px-5 border-2 border-[#f1f4f6] flex align-middle items-center bg-white group-hover:bg-primary-surface cursor-pointer">
                            <img src="/images/avatar-default.png" class="bg-[#e4e4e5] h-7 w-7 min-w-7 md:mr-2 rounded-full" name="profile-picture" alt="profile-picture" />
                            <div class="hidden md:flex items-center">
                                <div class=" flex flex-col text-gray-900">
                                    <div class="w-32 truncate text-xs font-bold">{{ $page.props.auth.user.name }}</div>
                                    <div class="w-32 truncate text-xs font-thin">{{ $page.props.auth.user.username }}</div>
                                </div>
                                <div class="ml-2">
                                    <bs-icon icon="chevron-down"></bs-icon>
                                </div>
                            </div>
                        </div>
                        <div class="hidden group-focus:block group-hover:block absolute top-11 mr-2 right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white focus:outline-none shadow-lg" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                            <div class="py-1" role="none">
                                <!-- Active: "bg-gray-100 text-gray-900", Not Active: "text-gray-700" -->
                                <Link href="/account" class="flex flex-row px-4 py-2 text-gray-900 text-sm hover:bg-primary-surface hover:rounded-md" role="menuitem" tabindex="-1" id="menu-item-0">
                                    <bs-icon icon="cog-8-tooth" class="mr-2"></bs-icon>
                                    Account settings
                                </Link>
                                <Link href="/logout" method="post" as="button" class="flex flex-row text-danger px-4 py-2 text-sm hover:bg-primary-surface hover:rounded-md w-full" role="menuitem" tabindex="-1" id="menu-item-2">
                                    <bs-icon icon="arrow-right-on-rectangle" class="mr-2"></bs-icon>
                                    Logout
                                </Link>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
        <div class="h-3 bg-primary-surface">
            <div class="pl-0 flex-1 flex transition-[padding-left] duration-300 ease-in-out" :class="[
                { 'lg:pl-20': sidemenu },
                { 'lg:pl-64': !sidemenu }
            ]">
                <div class="px-6 w-full">
                    <div class="bg-white px-8 rounded-t-xl h-3">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, computed } from 'vue';
import BsIcon from '@/Components/BsIcon.vue';
import { Link } from '@inertiajs/vue3';
import { useSidemenuStore } from '@/Stores/sidemenu';

const sideMenuStore = useSidemenuStore();

const emit = defineEmits(['toogleCollapse']);
const sidemenu = computed(()=>sideMenuStore.sidemenu);

const toggleSideMenu = sideMenuStore.toggleSidemenu;
const toggleDrawer = sideMenuStore.toggleDrawer;
</script>