<template>
    <div class="p-4 rounded-xl border border-gray-400 relative overflow-hidden">
        <div class="flex flex-row justify-between items-center">
            <div class="relative z-10">
                <div class="font-semibold text-sm sm:text-base text-gray-700">Revenue</div>
                <h1 class="font-bold text-xl sm:text-3xl my-1">Rp. {{ value }} Jt</h1>
                <div v-if="growth" class="font-semibold text-xs sm:text-sm flex items-center gap-1" :class="growth > 0 ? 'text-success' : 'text-danger'">
                    <BsIcon icon="arrow-trending-up" :size="16" v-if="growth > 0" />
                    <BsIcon icon="arrow-trending-down" :size="16" v-else />
                    {{ growth }}%
                    Last Month
                </div>
            </div>
            <BsIcon icon="chart-pie" :size="32" class="text-primary" />
        </div>
        <div ref="chartdiv" class="h-1/4 absolute bottom-0 -left-96 -right-96"></div>
    </div>
</template>

<script setup>
import { defineProps, ref, onMounted } from 'vue';

import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";
import am4themes_animated from "@amcharts/amcharts4/themes/animated";
import am4themes_pkt_themes from "@/Core/Config/am4themes_pkt_themes";
import BsIcon from '@/Components/BsIcon.vue';

const props = defineProps({
    dataSource: {
        type: Array,
        // Only for dummy data, you can remove this
        default: () => {
            let data = [];
            for (let i = 0; i < 12; i++) {
                data.push({
                    date: new Date(2023, 0, i + 1).toISOString().split('T')[0],
                    quantity: Math.floor(Math.random() * 100),
                });
            }
            return data;
        },
    },
    value: {
        type: Number,
        default: 1.23,
    },
    growth: {
        type: Number,
        default: 12,
    },
});

const chartdiv = ref(null);
onMounted(() => {
    am4core.addLicense(import.meta.env.VITE_AMCHARTS_LICENSE_KEY ?? "");
    am4core.useTheme(am4themes_animated);
    am4core.useTheme(am4themes_pkt_themes);

    // Create chart instance for line chart
    var chart = am4core.create(chartdiv.value, am4charts.XYChart);
    chart.logo?.dispose();
    chart.padding(0, 0, 0, 0);
    chart.margin(0, 0, 0, 0);

    // Add data
    chart.data = props.dataSource;

    // Create axes
    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "date";
    categoryAxis.renderer.grid.template.location = 0;

    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.min = 0;

    // Create series
    var series = chart.series.push(new am4charts.LineSeries());
    series.dataFields.valueY = "quantity";
    series.dataFields.categoryX = "date";
    series.strokeWidth = 2;
    series.tensionX = 0.77;

    // make the color to fill the line
    series.fill = am4core.color("#1268b3");
    series.fillOpacity = 0.03;

    // clear all the axis labels and ticks
    categoryAxis.renderer.labels.template.disabled = true;
    categoryAxis.renderer.ticks.template.disabled = true;
    valueAxis.renderer.labels.template.disabled = true;
    valueAxis.renderer.ticks.template.disabled = true;

    // clear background and grid
    categoryAxis.renderer.grid.template.disabled = true;
    valueAxis.renderer.grid.template.disabled = true;
});

</script>
