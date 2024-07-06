<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
        <div class="flex justify-between items-center flex-wrap gap-3">
            <div class="flex items-center gap-3 flex-wrap justify-center">
                <BsProfilePicture :npk="npk" class="rounded-full w-12 h-12" />
                <div class="text-center sm:text-left">
                    <h1 class="font-bold">Welcome,</h1>
                    <p class="font-light">{{ name }}</p>
                </div>
            </div>

            <BsButton @click="logout" class="w-full sm:w-fit" type="primary-outline" icon="arrow-left-on-rectangle">
                Logout
            </BsButton>
        </div>
    </div>
</template>

<script setup>
import BsButton from '@/Components/BsButton.vue';
import BsProfilePicture from '@/Components/BsProfilePicture.vue';
import { router, usePage } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    // ...
});

const npk = usePage().props.auth.user.npk;
const name = usePage().props.auth.user.name;

const logout = () => {
    router.post(route('logout'));
};

</script>
