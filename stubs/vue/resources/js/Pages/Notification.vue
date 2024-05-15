<template>
    <Head title="Notification" />
    <MainLayout title="Notification">
        <div>
            <ul>
                <li v-for="notificationGroupItem, date in notificationsGroup" :key="date">
                    <div class="flex space-x-2 mb-2 items-center w-full">
                        <div class="font-bold mb-2 text-lg">
                            {{ formatDate(date) }}
                        </div>
                        <div class="font-bold mb-2 text-gray-700 text-md italic">
                            {{ new Date(date).toLocaleDateString('id-ID') }}
                        </div>
                    </div>
                    <el-timeline>
                        <el-timeline-item v-for="notification in notificationGroupItem" :key="notification.id"
                            :timestamp="notification.time" placement="top" color="#f47920">
                            <div class="rounded-lg border border-gray-500 p-2">
                                <div class="flex flex-row">
                                    <div>
                                        <div class=" font-bold ">{{ notification.data.title }}</div>
                                        <div>{{ notification.data.message }}</div>
                                    </div>
                                </div>
                            </div>
                        </el-timeline-item>
                    </el-timeline>
                </li>
            </ul>
            <div class="flex flex-row justify-end">
                <el-pagination 
                    v-model:currentPage="currentPage"
                    :page-size="paginatedNotifications.per_page" 
                    :total="paginatedNotifications.total"
                    layout="prev, pager, next" background 
                    @current-change="getNotification" />
            </div>
        </div>
    </MainLayout>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import moment from 'moment';

const paginatedNotifications = ref([]);
const notificationsGroup = computed(() => {
    const groupedData = {};
    if (Object.keys(paginatedNotifications.value).length > 0) {
        paginatedNotifications.value.data.forEach((notification) => {
            const date = notification.date;
            groupedData[date] = groupedData[date] || [];
            groupedData[date].push(notification);
        });
    }
    return groupedData;
});

function formatDate(strDate) {
    const date = new Date(strDate);
    const relativeTime = moment(date).calendar(null, {
        sameDay: '[Today]',
        lastDay: '[Yesterday]',
        lastWeek: '[Last] dddd',
        sameElse: 'L'
    });
    return relativeTime;
}

const currentPage = ref(1);

onMounted(async () => {
    getNotification(currentPage.value);
});

function getNotification(page) {
    currentPage.value = page;
    axios.get(route('notification.data')+'/?page='+page)
        .then((response) => {
            var responseData = response.data;
            paginatedNotifications.value = responseData;
            console.log(notificationsGroup.value);
            window.scrollTo(0, 0);
        });
}

</script>