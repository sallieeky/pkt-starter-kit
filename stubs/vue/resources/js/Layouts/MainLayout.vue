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
                            <main :key="$page.url" class="bg-white rounded-xl p-8">
                                <p class="text-xl font-bold mb-5">{{ title }}</p>
                                <slot />
                            </main>
                        </Transition>
                    </div>
                </div>
            </div>
            <Footer/>
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

const sideMenuStore = useSidemenuStore();

const sidemenu = computed(()=>sideMenuStore.sidemenu);

const props = defineProps({
    title:{
        type: String
    }
});

var userId = ref(usePage().props.auth.user.user_id);

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
