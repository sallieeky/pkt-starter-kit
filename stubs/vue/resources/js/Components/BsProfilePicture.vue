<template>
    <img :src="route('account-picture', {user: npk ?? 'default'})" class="bg-[#e4e4e5]" name="profile-picture" alt="profile-picture"/>
</template>
<script setup>

const props = defineProps({
    npk:{
        type: String,
        required: false
    }
});

</script>