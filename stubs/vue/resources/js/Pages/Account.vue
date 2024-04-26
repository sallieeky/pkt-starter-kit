<template>
    <Head title="Account" />
    <MainLayout title="Account">
        <div class="grid grid-cols-12 grid-flow-row-dense gap-4">
            <div class="col-span-12 p-4 rounded-xl border border-gray-400">
                <div class="flex flex-row justify-between mb-4">
                    <div>
                        <div class="font-bold">Account Information</div>
                        <div class="font-thin text-gray-700 text-sm"></div>
                    </div>
                </div>
                <hr/>
                
                <div>
                    <div class="grid grid-cols-12 grid-flow-row-dense gap-4">
                        <div class="col-span-12 lg:col-span-3 p-4">
                            <BsProfilePicture/>
                        </div>
                        <div class="col-span-12 lg:col-span-9 p-4">
                            <table class="w-full">
                                <tr>
                                    <td class="font-bold w-60">Nama </td>
                                    <td>{{ user.name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold w-60">Personnal Number (NPK) </td>
                                    <td>{{ user.npk ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold w-60">Email </td>
                                    <td>{{ user.email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold w-60">Departement</td>
                                    <td>{{ '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold w-60">Jabatan</td>
                                    <td>{{ '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold w-60">Role</td>
                                    <td>
                                        <div class="flex flex-row" v-if="user.roles.length > 0">
                                            <div class="bg-primary rounded-full px-3 py-1 text-white m-px w-min text-xs">
                                                {{ user.roles[0].name }}
                                            </div>
                                            <el-popover placement="top" :width="150" trigger="hover" v-if="user.roles.length > 1">
                                            <template #reference>
                                                <div class=" bg-primary-hover rounded-full px-2 py-1 text-white m-px text-xs cursor-pointer w-auto">+ {{ user.roles.length - 1 }}</div>
                                            </template>
                                            <template #default>
                                                <div class="w-full flex flex-col justify-center items-center">
                                                    <div class="bg-primary rounded-full px-3 py-1 text-white m-px text-xs w-fit" v-for="role in user.roles.slice(1, user.roles.length)">
                                                        {{ role.name }}
                                                    </div>
                                                </div>
                                            </template>
                                        </el-popover>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
<script setup>
import { ref } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import BsProfilePicture from '@/Components/BsProfilePicture.vue';
import { Head, usePage } from '@inertiajs/vue3';

var user = ref(usePage().props.auth.user);
</script>