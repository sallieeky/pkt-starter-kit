<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
        <div class="flex flex-row justify-between mb-4">
            <div>
                <div class="font-bold">Production By Product</div>
                <div class="font-thin text-gray-700 text-sm">This chart shows the production of each product</div>
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
        // Only for dummy data, you can remove this
        default: () => {
            let data = [];
            for (let i = 0; i < 7; i++) {
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
    am4core.addLicense(import.meta.env.VITE_AMCHARTS_LICENSE_KEY ?? "");
    am4core.useTheme(am4themes_animated);
    am4core.useTheme(am4themes_pkt_themes);

    // Create chart instance for pie chart
    var chart = am4core.create(chartdiv.value, am4charts.PieChart);
    chart.logo?.dispose();

    // Add data
    chart.data = props.dataSource;

    // Create series
    var pieSeries = chart.series.push(new am4charts.PieSeries());
    pieSeries.dataFields.value = "quantity";
    pieSeries.dataFields.category = "product";
    pieSeries.dataFields.hidden = "hidden";

    // Add legend
    chart.legend = new am4charts.Legend();
    chart.legend.position = "bottom";
    chart.legend.fontSize = 10;

    // Customize legend marker
    var markerTemplate = chart.legend.markers.template;
    markerTemplate.width = 15;
    markerTemplate.height = 15;

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
    title.text = "Production By Product";
    title.fontSize = 20;
    title.fontWeight = "bold";
    title.marginBottom = 8;
    title.fill = am4core.color("#333333");
    title.align = "center";
});

</script>
