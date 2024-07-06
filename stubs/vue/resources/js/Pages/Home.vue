<template>
    <Head title="Dashboard" />
    <MainLayout title="Dashboard">
        <Welcome class="mb-3" />
        <div class="grid grid-cols-2 gap-3 mb-3">
            <RevenueStat />
            <ProductionStat />
        </div>
        <div class="grid grid-cols-3 gap-3 mb-3">
            <ProductionGraph class="col-span-full md:col-span-2" :dataSource="dataSource" />
            <ProductionSummary class="col-span-full md:col-auto" :dataSource="dataSource" />
        </div>
        <ReportNewProduction :product="dataSource" />
    </MainLayout>
</template>
<script setup>
import { ref } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import RevenueStat from '@/Widgets/Home/RevenueStat.vue';
import ProductionStat from '@/Widgets/Home/ProductionStat.vue';
import Welcome from '@/Widgets/Home/Welcome.vue';
import ProductionGraph from '@/Widgets/Home/ProductionGraph.vue';
import ProductionSummary from '@/Widgets/Home/ProductionSummary.vue';
import ReportNewProduction from '@/Widgets/Home/ReportNewProduction.vue';

const dataSource = [{
    "product_id": 1,
    "product": "Ammonia",
    "quantity": 62567,
    "target": 60000,
}, {
    "product_id": 2,
    "product": "Urea",
    "quantity": 85293,
    "target": 90000
}, {
    "product_id": 3,
    "product": "NPK",
    "quantity": 40568,
    "target": 50000
}, {
    "product_id": 4,
    "product": "Other",
    "quantity": 32476,
    "target": 50000
}];

</script>
