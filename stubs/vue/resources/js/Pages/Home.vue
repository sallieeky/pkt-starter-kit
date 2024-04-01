<template>
    <Head title="Dashboard" />
    <MainLayout title="Dashboard">
        <div class="grid grid-cols-12 grid-flow-row-dense gap-4">
            <div class="col-span-12 lg:col-span-4 p-4 rounded-xl border border-gray-400 flex-1">
                <div class="flex flex-row justify-between mb-4">
                    <div>
                        <div class="font-bold">Monthly Production</div>
                        <div class="font-thin text-gray-700 text-sm">Agustus 2023</div>
                    </div>
                    <div>
                    </div>
                </div>
                <div ref="chartdiv" class="h-80 w-full"></div>
            </div>
            <div class="col-span-12 lg:col-span-8 p-4 rounded-xl border border-gray-400">
                <div class="flex flex-row justify-between mb-4">
                    <div>
                        <div class="font-bold">Table Test</div>
                        <div class="font-thin text-gray-700 text-sm">Sub title</div>
                    </div>
                    <div>
                    </div>
                </div>
                <DxDataGrid :data-source="dataSource" key-expr="product_id" @cell-prepared="onCellPrepared" :column-auto-width="true">
                    <DxFilterRow :visible="true" />
                    <DxScrolling :use-native="true"/>
                    <DxSelection select-all-mode="page" show-check-boxes-mode="always" mode="multiple" />
                    <DxColumn data-field="product" caption="Product" />
                    <DxColumn data-field="quantity" caption="Quantity">
                        <DxFormat type="fixedPoint" :precision="0" />
                    </DxColumn>
                    <DxColumn data-field="target" caption="Target">
                        <DxFormat type="fixedPoint" :precision="0" />
                    </DxColumn>
                </DxDataGrid>
            </div>
            <div class="col-span-12 p-4 rounded-xl border border-gray-400">
                <div class="flex flex-row justify-between mb-4">
                    <div>
                        <div class="font-bold">Form Test</div>
                        <div class="font-thin text-gray-700 text-sm">Sub title</div>
                    </div>

                </div>
                <div>
                    <el-form :model="form" label-width="200px" label-position="left" require-asterisk-position="right">
                        <el-form-item label="Activity name" :required="true">
                            <el-input v-model="form.name" class=" rounded-lg"/>
                        </el-form-item>
                        <el-form-item label="Activity zone">
                            <el-select v-model="form.region" placeholder="please select your zone">
                                <el-option label="Zone one" value="shanghai" />
                                <el-option label="Zone two" value="beijing" />
                            </el-select>
                        </el-form-item>
                        <el-form-item label="Activity time">
                            <el-col :span="11">
                                <el-date-picker v-model="form.date1" type="date" placeholder="Pick a date"
                                    style="width: 100%" />
                            </el-col>
                            <el-col :span="2" class="text-center">
                                <span class="text-gray-500">-</span>
                            </el-col>
                            <el-col :span="11">
                                <el-time-picker v-model="form.date2" placeholder="Pick a time" style="width: 100%" />
                            </el-col>
                        </el-form-item>
                        <el-form-item label="Instant delivery">
                            <el-switch v-model="form.delivery" />
                        </el-form-item>
                        <el-form-item label="Activity type">
                            <el-checkbox-group v-model="form.type">
                                <el-checkbox label="Online activities" name="type" />
                                <el-checkbox label="Promotion activities" name="type" />
                                <el-checkbox label="Offline activities" name="type" />
                                <el-checkbox label="Simple brand exposure" name="type" />
                            </el-checkbox-group>
                        </el-form-item>
                        <el-form-item label="Resources">
                            <el-radio-group v-model="form.resource">
                                <el-radio label="Sponsor" />
                                <el-radio label="Venue" />
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item label="Activity form">
                            <el-input v-model="form.desc" type="textarea" />
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="onSubmit">
                                <BsIcon icon="rocket-launch"></BsIcon> Submit
                            </el-button>
                            <el-button type="warning">Cancel</el-button>
                            <el-button type="danger">Delete</el-button>
                            <el-button type="success">Approve</el-button>
                            <el-button>Cancel</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';

import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";
import am4themes_animated from "@amcharts/amcharts4/themes/animated";
import am4themes_pkt_themes from "@/Core/Config/am4themes_pkt_themes";
import { ElLoading } from 'element-plus';

import {
    DxDataGrid,
    DxColumn,
    DxFormat,
    DxFilterRow,
    DxSelection,
    DxScrolling,
} from 'devextreme-vue/data-grid';
import BsIcon from '@/Components/BsIcon.vue';

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

// Devextreme
const columns = ['product', 'quantity', 'target'];
const onCellPrepared = (e) => {
    if (e.rowType === 'header' && e.column.dataType != 'boolean') {
        e.cellElement.style.textAlign = 'left';
    }
}


// Amchart
const chartdiv = ref(null);
onMounted(() => {
    am4core.addLicense("CH283435101");
    am4core.useTheme(am4themes_animated);
    am4core.useTheme(am4themes_pkt_themes);

    var chart = am4core.create(chartdiv.value, am4charts.PieChart);

    chart.data = dataSource;

    var pieSeries = chart.series.push(new am4charts.PieSeries());
    pieSeries.dataFields.value = "quantity";
    pieSeries.dataFields.category = "product";
    pieSeries.dataFields.hidden = "hidden";

    chart.innerRadius = am4core.percent(40);

    pieSeries.labels.template.disabled = true;
    pieSeries.ticks.template.disabled = true;

    pieSeries.slices.template.tooltipText = "";

    chart.legend = new am4charts.Legend();
    chart.legend.position = "bottom";
    chart.legend.fontSize = 10;

    var markerTemplate = chart.legend.markers.template;
    markerTemplate.width = 15;
    markerTemplate.height = 15;
});

// Element plus
const form = reactive({
    name: '',
    region: '',
    date1: '',
    date2: '',
    delivery: false,
    type: [],
    resource: '',
    desc: '',
});

const onSubmit = () => {
    const loading = ElLoading.service({
        lock: true,
        customClass: "spinner-loading-img",
        spinner: "disable-default-spinner",
    });
    setTimeout(() => {
        loading.close()
    }, 2000);
}

</script>