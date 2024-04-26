<template>
    <img src="/images/avatar-default.png" class="bg-[#e4e4e5]" name="profile-picture" alt="profile-picture" v-if="pictNotFound"/>
    <img :src="'https://leader.pupukkaltim.com/public/foto-thumbnail/'+props.npk+'.jpg'" @error="()=>pictNotFound=true" class="bg-[#e4e4e5]" name="profile-picture" alt="profile-picture" v-else/>
</template>
<script setup>
import { ref } from 'vue';

const props = defineProps({
    npk:{
        type: String,
        required: false
    }
});

var pictNotFound = ref(false);

</script>