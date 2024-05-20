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
                    <div class="hidden md:block group relative" v-if="isEnableGlobalSearch">
                        <el-input class="peer" v-model="globalSearch" @change="search" placeholder="Search..."  size="large">
                            <template #prefix>
                                <BsIcon icon="magnifying-glass"></BsIcon>
                            </template>
                        </el-input>
                        <div class="hidden peer-focus-within:block hover:block absolute top-13 right-0 z-10 mt-2 w-64 md:w-[26rem] max-h-[24rem] overflow-y-auto origin-top-right rounded-md bg-white focus:outline-none shadow-lg" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                            <div class="p-2 cursor-default" role="none">
                                <div v-for="(records, model) in globalSearchResult">
                                    <div class="sticky p-2 text-gray-700 font-semibold text-lg">{{ model }}</div>
                                    <div v-for="record in records">
                                        <a :href="record.url" class="block text-sm px-5 py-2 hover:text-primary">{{ record.value }}</a>
                                    </div>
                                </div>
                                <div v-if="globalSearchResult.length == 0" class="text-center text-gray-900 text-sm py-2 cursor-default italic">
                                    No Result
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="md:hidden" v-if="isEnableGlobalSearch">
                        <BsIconButton icon="magnifying-glass" @click="showSearch"></BsIconButton>
                    </div>
                    
                    <button class="group relative">
                        <el-badge :is-dot="notifications.length !== 0" :offset="[-10,10]" class="mx-2 p-2 hover:bg-slate-100 rounded-full cursor-pointer">
                            <bs-icon icon="bell"></bs-icon>
                        </el-badge>
                        <div class="hidden group-focus:block group-hover:block absolute top-13 mr-2 right-0 z-10 mt-2 w-64 md:w-[24rem] max-h-[24rem] overflow-y-auto origin-top-right rounded-md bg-white focus:outline-none shadow-lg" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                            <div class="p-2 text-start border-b-2 fixed bg-white w-64 md:w-[24rem] cursor-default flex justify-between items-center">
                                <div class="font-bold">Notifications</div>
                                <div class="text-xs text-primary cursor-pointer" @click="readNotification()" v-if="notifications.length > 0">Read all</div>
                            </div>
                            <div class="py-1 mt-12" role="none">
                                <div v-for="notifications,date in notificationsGroup">
                                    <div class="text-xs text-gray-500">{{ DatetimeFormatter.formatDateHumanReadable(date) }}</div>
                                    <div v-for="notification in notifications">
                                        <div @click="notificationAction(notification)" class="group/item text-start flex flex-row px-4 py-2 text-gray-900 text-sm hover:bg-primary-surface hover:rounded-md">
                                            <div class="w-3/4">
                                                <div class="text-[10px] italic text-gray-500">
                                                    {{ DatetimeFormatter.formatTime(notification.created_at) }}
                                                </div>
                                                <h1 class="font-bold">{{ notification.data.title }}</h1>
                                                <p class="text-xs" v-html="notification.data.message"></p>
                                            </div>
                                            <div @click.stop="readNotification(notification.id)" class="hidden ml-auto group-hover/item:block text-[.75rem] hover:group/item:bg-white hover:text-primary">
                                                Mark as read
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="notifications.length == 0" class="text-center italic text-gray-900 text-sm py-8 cursor-default">
                                    No notifications
                                </div>
                                <div class="cursor-default flex justify-center items-center mt-4">
                                    <a :href="route('notification.browse')" class="text-sm italic text-primary hover:text-primary-hover">view all</a>
                                </div>
                            </div>
                        </div>
                    </button>
                    <button class="group text-start">
                        <div class="shrink-0 rounded-lg py-2 px-5 border-2 border-[#f1f4f6] flex align-middle items-center bg-white group-hover:bg-primary-surface cursor-pointer">
                            <BsProfilePicture class="w-10 h-10 md:mr-2 rounded-full shadow-lg" :npk="user.npk"/>
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
                        <div class="hidden group-focus:block group-hover:block absolute top-13 mr-2 right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white focus:outline-none shadow-lg" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                            <div class="py-1" role="none">
                                <Link href="/account" class="flex flex-row px-4 py-2 text-gray-900 text-sm hover:bg-primary-surface hover:rounded-md" role="menuitem" tabindex="-1" id="menu-item-0">
                                    <bs-icon icon="user" class="mr-2"></bs-icon>
                                    Account
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
        <el-dialog v-model="searchVisible" :show-close="false" :fullscreen="true" :align-center="true" :lock-scroll="true" :append-to-body="true" :destroy-on-close="true" class="flex flex-col flex-grow">  
            <div class="flex flex-none">
                <el-input ref="narrowSearchRef" v-model="globalSearch" @change="search" placeholder="Search..."  size="large">
                    <template #prefix>
                        <BsIcon icon="magnifying-glass"></BsIcon>
                    </template>
                </el-input>
                <BsIconButton icon="x-mark" @click="hideSearch"></BsIconButton>
            </div>
            <div class="mt-8 h-[80vh]">
                <div class="p-4 cursor-default overflow-y-auto h-full">
                    <div v-for="(records, model) in globalSearchResult"  class="space-y-2">
                        <div class="sticky font-bold text-gray-700 text-lg">{{ model }}</div>
                        <div v-for="record in records" class="rounded-md border border-gray-400">
                            <a :href="record.url" class="block text-sm px-5 py-2 hover:text-primary">{{ record.value }}</a>
                        </div>
                    </div>
                    <div v-if="globalSearchResult.length == 0" class="text-center text-gray-900 text-sm py-2 cursor-default italic">
                        No Result
                    </div>
                </div>
            </div>
        </el-dialog>
    </div>
</template>
<script setup>
import { ref, computed } from 'vue';
import BsIcon from '@/Components/BsIcon.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useSidemenuStore } from '@/Stores/sidemenu';
import BsProfilePicture from '@/Components/BsProfilePicture.vue';
import BsIconButton from '@/Components/BsIconButton.vue';
import DatetimeFormatter from '@/Core/Helpers/datetime-formatter.js';
import moment from 'moment';

const sideMenuStore = useSidemenuStore();

const emit = defineEmits(['toogleCollapse']);
const sidemenu = computed(()=>sideMenuStore.sidemenu);

const toggleSideMenu = sideMenuStore.toggleSidemenu;
const toggleDrawer = sideMenuStore.toggleDrawer;

var user = ref(usePage().props.auth.user);
let notifications = computed(() => usePage().props.notifications);

const notificationsGroup = computed(() => {
    const groupedData = {};
    if (Object.keys(notifications.value).length > 0) {
        notifications.value.forEach((notification) => {
            const createdAt = notification.created_at;
            const date = moment(createdAt).format('YYYY-MM-DD');
            groupedData[date] = groupedData[date] || [];
            groupedData[date].push(notification);
        });
    }
    return groupedData;
});

const readNotification = (notification) => {
    axios.post(route('notification.mark_as_read'), {
        notification: notification,
    }).then(()=>{
        router.reload({only: ['notifications']})
    });
}
const notificationAction = (notification) => {
    axios.post(route('notification.mark_as_read'), {
        notification: notification.id,
    }).then(()=>{
        router.reload({only: ['notifications']})
    });
    if (notification.data?.url == null) return;
    router.visit(notification.data?.url);
}

let globalSearch = ref('');
let globalSearchResult = ref([]);
let isEnableGlobalSearch = ref(usePage().props.isEnableGlobalSearch);

const search = () => {
    if (globalSearch.value.length < 1) {
        globalSearchResult.value = [];
        return;
    }
    axios.get(route('global.search'), {
        params: {
            search: globalSearch.value,
        }
    }).then((response) => {
        globalSearchResult.value = response.data.data;
    });
}

var narrowSearchRef = ref();
const searchVisible = ref(false);

const showSearch = ()=>{
    searchVisible.value = true;
}

const hideSearch = ()=>{
    searchVisible.value = false;
    globalSearch.value = "";
    globalSearchResult.value = [];
}

</script>
