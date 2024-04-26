<template>
    <img src="/images/avatar-default.png" class="bg-[#e4e4e5]" name="profile-picture" alt="profile-picture" v-if="pictNotFound"/>
    <img :src="'https://leader.pupukkaltim.com/public/foto-thumbnail/'+user.npk+'.jpg'" @error="()=>pictNotFound=true" class="bg-[#e4e4e5]" name="profile-picture" alt="profile-picture" v-else/>
</template>
<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

var pictNotFound = ref(false);
var user = ref(usePage().props.auth.user);

</script>