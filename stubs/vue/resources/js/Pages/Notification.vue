<template>
    <Head title="Notification" />
    <MainLayout title="Notification">
        <div>
            <ul>
                <li v-for="notificationGroupItem, date in notificationsGroup" :key="date">
                    <div class="flex space-x-2 mb-2 items-center w-full">
                        <div class="font-bold mb-2 text-lg">
                            {{ DatetimeFormatter.formatDateHumanReadable(date) }}
                        </div>
                        <div class="font-bold mb-2 text-gray-700 text-md italic">
                            {{ DatetimeFormatter.formatDateCalender(date) }}
                        </div>
                    </div>
                    <el-timeline>
                        <el-timeline-item v-for="notification in notificationGroupItem" :key="notification.id"
                            :timestamp="DatetimeFormatter.formatTime(notification.created_at)" placement="top" color="#f47920">
                            <div class="rounded-lg border border-gray-500 p-2 hover:bg-primary-surface group">
                                <a class="flex flex-row" :href="notification.data.url ?? 'javascript:void(0)'">
                                    <div>
                                        <a class="text-md font-bold group-hover:text-primary">{{ notification.data.title }}</a>
                                        <div v-html="notification.data.message"></div>
                                    </div>
                                </a>
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
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import moment from 'moment';

import DatetimeFormatter from '@/Core/Helpers/datetime-formatter.js';

const paginatedNotifications = ref([]);
const notificationsGroup = computed(() => {
    const groupedData = {};
    if (Object.keys(paginatedNotifications.value).length > 0) {
        paginatedNotifications.value.data.forEach((notification) => {
            const createdAt = notification.created_at;
            const date = moment(createdAt).format('YYYY-MM-DD');
            groupedData[date] = groupedData[date] || [];
            groupedData[date].push(notification);
        });
    }
    return groupedData;
});

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
            window.scrollTo(0, 0);
        });
}

</script>