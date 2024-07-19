<template>
    <div class="flex">
        <Header/>
        <div class="flex flex-col min-h-screen w-screen overflow-hidden">
            <!-- sidebar -->
            <Sidebar/>
            <!-- content -->
            <div class="pl-0 flex-grow pt-20 transition-[padding-left] duration-300 ease-in-out overflow-hidden" :class="[
                {'lg:pl-20':sidemenu},
                {'lg:pl-64':!sidemenu}
            ]">
                <div class="flex-1">
                    <div class="px-6 pb-6 w-full">
                        <Transition name="page" mode="out-in" appear>
                            <main :key="$page.url" class="bg-white rounded-xl p-6 px-4 sm:p-8 min-h-[83vh]">
                                <!-- Header Action -->
                                <div class="flex items-center justify-between mb-5">
                                    <p v-if="back === false && backConfirm === false" class="sm:text-xl font-bold">{{ title }}</p>
                                    <div v-else-if="back === true || backConfirm === true" class="flex items-center gap-0 sm:gap-2 text-primary-main cursor-pointer" @click="backAction">
                                        <BsIcon icon="chevron-left" />
                                        <p class="sm:text-xl font-bold">{{ title }}</p>
                                    </div>

                                    <div class="flex items-center gap-1">
                                        <div v-if="$slots['header-action']" class="hidden sm:block">
                                            <slot name="header-action" />
                                        </div>
                                        <div v-if="$slots['header-action']" class="sm:hidden">
                                            <el-dropdown trigger="click" placement="bottom-end">
                                                <span class="el-dropdown-link">
                                                    <BsIcon icon="ellipsis-vertical" />
                                                </span>
                                                <template #dropdown>
                                                    <el-dropdown-menu>
                                                        <div class="flex flex-col">
                                                            <slot name="header-action" />
                                                        </div>
                                                    </el-dropdown-menu>
                                                </template>
                                            </el-dropdown>
                                        </div>

                                        <div v-if="$slots['header-action-dropdown']">
                                            <el-dropdown trigger="click" placement="bottom-end">
                                                <span class="el-dropdown-link">
                                                    <BsIcon icon="ellipsis-vertical" />
                                                </span>
                                                <template #dropdown>
                                                    <el-dropdown-menu>
                                                        <div class="flex flex-col">
                                                            <slot name="header-action-dropdown" />
                                                        </div>
                                                    </el-dropdown-menu>
                                                </template>
                                            </el-dropdown>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Header -->

                                <!-- Content -->
                                <slot />
                                <!-- End Content -->

                                <!-- Fixed Footer Action -->
                                <div v-if="$slots['footer-action']" class="flex items-center gap-1 mt-5">
                                    <slot name="footer-action" />
                                </div>

                                <div v-if="$slots['footer-action-fixed']" class="fixed bottom-0 left-0 lg:ml-6 right-0 bg-white border-t border-gray-200 py-4 px-6 sm:px-8 rounded-t-xl shadow-[0px_10px_240px_0px_#00000024]"
                                    :class="[
                                        {'lg:left-20':sidemenu},
                                        {'lg:left-64':!sidemenu}
                                    ]"
                                >
                                    <slot name="footer-action-fixed" />
                                </div>
                                <!-- End Fixed Footer Action -->
                            </main>
                        </Transition>
                    </div>
                </div>
            </div>
            <Footer :collapsed="sidemenu" />
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import Header from './Components/Header.vue';
import Sidebar from './Components/Sidebar.vue';
import Footer from './Components/Footer.vue';
import { useSidemenuStore } from '@/Stores/sidemenu';
import { router, usePage } from '@inertiajs/vue3';
import { ElNotification } from 'element-plus';
import BsIcon from '@/Components/BsIcon.vue';

const sideMenuStore = useSidemenuStore();

const sidemenu = computed(()=>sideMenuStore.sidemenu);

const props = defineProps({
    title:{
        type: String
    },
    back: {
        type: Boolean,
        default: false
    },
    backConfirm: {
        type: Boolean,
        default: false
    }
});

var userId = ref(usePage().props.auth.user.user_id);

const backAction = () => {
    if(props.backConfirm){
        ElMessageBox.confirm(
            'Any unsaved changes will be lost.',
            'Leave The Page?',
            {
                confirmButtonText: 'Leave',
                cancelButtonText: 'Stay',
                type: 'warning',
            }
        )
            .then(() => {
                window.history.back();
            })
            .catch(() => {
                ElMessage({
                    type: 'info',
                    message: 'Stay on the page.',
                })
            })
    } else{
        window.history.back();
    }
}

if (import.meta.env.VITE_BROADCAST_DRIVER !== 'log') {
    Echo.private("App.Models.User."+userId.value)
        .notification((notification) => {
            // router.reload({only: ['notifications']});
            ElNotification.closeAll();
            ElNotification({
                title: notification.title,
                message: notification.message,
                position: 'bottom-right',
                type: 'info'
            });
        });
}
</script>

<style scoped>
    .page-enter-from,
    .page-leave-to {
        opacity: 0;
    }

    .page-enter-active,
    .page-leave-active {
        transition: opacity 0.3s ease-out;
    }
</style>
