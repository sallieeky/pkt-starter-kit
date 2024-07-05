<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
        <div class="flex flex-row justify-between mb-4">
            <div>
                <div class="font-bold">WidgetName</div>
                <div class="font-thin text-gray-700 text-sm">Subtitle for your chart</div>
            </div>
        </div>
        <div ref="chartdiv" class="h-80 w-full"></div>
    </div>
</template>

<script setup>
import { defineProps, ref, onMounted } from 'vue';

import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";
import am4themes_animated from "@amcharts/amcharts4/themes/animated";
import am4themes_pkt_themes from "@/Core/Config/am4themes_pkt_themes";

const props = defineProps({
    dataSource: {
        type: Array,
        required: true,
        // Only for dummy data, you can remove this
        default: () => {
            let data = [];
            for (let i = 0; i < 10; i++) {
                data.push({
                    product: `Product ${i + 1}`,
                    quantity: Math.floor(Math.random() * 100),
                });
            }
            return data;
        },
    }
});

const chartdiv = ref(null);
onMounted(() => {
    am4core.addLicense("CH283435101");
    am4core.useTheme(am4themes_animated);
    am4core.useTheme(am4themes_pkt_themes);

    // Create chart instance for bar chart
    var chart = am4core.create(chartdiv.value, am4charts.XYChart);

    // Add data
    chart.data = props.dataSource;

    // Create axes
    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "product";
    categoryAxis.renderer.grid.template.location = 0;

    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.min = 0;

    // Create series
    var series = chart.series.push(new am4charts.ColumnSeries());
    series.dataFields.valueY = "quantity";
    series.dataFields.categoryX = "product";
    series.columns.template.strokeWidth = 0;

    // Add cursor
    chart.cursor = new am4charts.XYCursor();
    chart.cursor.lineX.disabled = false;
    chart.cursor.lineY.disabled = false;

    // Add export menu
    chart.exporting.menu = new am4core.ExportMenu();
    chart.exporting.menu.align = "left";
    chart.exporting.menu.verticalAlign = "top";
    chart.exporting.menu.items = [{
        "label": "...",
        "menu": [{
            "type": "png",
            "label": "PNG"
        }, {
            "type": "jpg",
            "label": "JPG"
        }, {
            "type": "svg",
            "label": "SVG"
        }, {
            "type": "pdf",
            "label": "PDF"
        }]
    }];

    // Add title
    var title = chart.titles.create();
    title.text = "Monthly Production";
    title.fontSize = 20;
    title.marginBottom = 20;
    title.marginTop = 20;
    title.fontWeight = "bold";
    title.fill = am4core.color("#333333");
    title.align = "center";
});

</script>
