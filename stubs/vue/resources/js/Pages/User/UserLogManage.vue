<template>

    <Head title="User Log" />
    <MainLayout title="User Log">
        <div class="flex flex-col">
            <DxDataGrid ref="datagridRef" :data-source="logData" :remote-operations="false" :item-per-page="10"
                :column-auto-width="true" @exporting="onExporting">
                <DxFilterRow :visible="true" />
                <DxExport :enabled="true" />
                <DxColumnChooser :enabled="true" mode="select" />
                <DxPaging :page-size="10" />
                <DxPager :visible="true" :allowed-page-sizes="[10, 20, 50]" :show-page-size-selector="true" />
                <DxToolbar>
                    <DxItem location="before" template="leftToolbar" />
                    <DxItem name="columnChooserButton" />
                    <DxItem name="exportButton" />
                </DxToolbar>
                <template #leftToolbar>
                    <div class="flex flex-row w-80">
                        <el-select v-model="fileSelected" @change="getLogData" width="300">
                            <el-option v-for="filename in logFiles" :value="filename">{{ filename }}</el-option>
                        </el-select>
                    </div>
                </template>
                <DxColumn data-field="status_code" caption="Code" width="70" alignment="center"
                    cell-template="status-code" />
                <template #status-code="{ data }">
                    <span v-if="data.data.status_code >= 200 && data.data.status_code < 300"
                        class="px-2 py-1 rounded-md bg-primary text-white text-[10px]">{{ data.data.status_code
                        }}</span>
                    <span v-else-if="data.data.status_code >= 300 && data.data.status_code < 400"
                        class="px-2 py-1 rounded-md bg-secondary text-white text-[10px]">{{ data.data.status_code
                        }}</span>
                    <span v-else class="px-2 py-1 rounded-md bg-danger text-white text-[10px]">{{ data.data.status_code
                        }}</span>
                </template>
                <DxColumn data-field="method" caption="Method" width="100" alignment="center" />
                <DxColumn data-field="path" caption="Path" minWidth="200" />
                <DxColumn data-field="timestamp" caption="Timestamp" data-type="datetime" format="dd/MM/yyyy HH:mm"
                    width="160" />
                <DxColumn data-field="ip_client" caption="IP Client" width="100" data-type="string" />
                <DxColumn data-field="user_id" caption="UId" width="80" />
                <DxColumn data-field="name" caption="User Name" minWidth="200" />
                <DxMasterDetail :enabled="true" template="masterDetailTemplate" />
                <template #masterDetailTemplate="{ data: logData }">
                    <div class="w-full mx-auto bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="px-6 py-4">
                            <div class="font-bold text-md mb-2">{{ logData.data.status_name }}</div>
                            <table class="text-gray-700 text-sm w-full">
                                <tr>
                                    <td class="font-bold align-top pr-4">Path</td>
                                    <td class="whitespace-normal">: {{ logData.data.path }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold align-top pr-4">Method</td>
                                    <td class="whitespace-normal">: {{ logData.data.method }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold align-top pr-4">Status Code</td>
                                    <td class="whitespace-normal">: {{ logData.data.status_code }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold align-top pr-4">User ID</td>
                                    <td class="whitespace-normal">: {{ logData.data.user_id }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold align-top pr-4">Name</td>
                                    <td class="whitespace-normal">: {{ logData.data.name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold align-top pr-4">IP Client</td>
                                    <td class="whitespace-normal">: {{ logData.data.ip_client }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold align-top pr-4">User Agent</td>
                                    <td class="whitespace-normal">: {{ logData.data.user_agent }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold align-top pr-4">Timestamp</td>
                                    <td class="whitespace-normal">: {{ logData.data.timestamp }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold align-top pr-4">Log Status</td>
                                    <td class="whitespace-normal">: {{ logData.data.log_status }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="bg-white w-full mx-auto shadow-md rounded-lg overflow-hidden mt-2" v-if="logData.data.request_body != null && logData.data.request_body != undefined && Object.keys(logData.data.request_body).length > 0">
                        <div class="px-6 py-4">
                            <div class="font-bold text-md mb-2">Request Body</div>
                            <div class="bg-gray-200 p-2 text-sm">
                                <pre
                                    class="whitespace-pre-wrap">{{ JSON.stringify(logData.data.request_body, null, 2) }}</pre>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white w-full mx-auto shadow-md rounded-lg overflow-hidden mt-2" v-if="logData.data.errors != null && logData.data.errors != undefined && Object.keys(logData.data.errors).length > 0">
                        <div class="px-6 py-4">
                            <div class="font-bold text-md mb-2 text-white p-2 bg-danger">{{ logData.data.errors.error_class }}</div>
                            <div class="font-bold text-sm mb-2  p-2">{{ logData.data.errors.message }}</div>
                            <div class="border-t border-gray-400 my-2"></div>
                            <div class="text-gray-700 text-base">
                                <div v-for="(trace, index) in logData.data.errors.stack_trace.slice(0, 3)" :key="index" class="mb-4 bg-gray-200 p-2">
                                    <table class="text-sm">
                                        <tr>
                                            <td class="font-bold align-top pr-4">File</td>
                                            <td class="whitespace-normal">{{ trace.file }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold align-top pr-4">Line</td>
                                            <td class="whitespace-normal">{{ trace.line }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold align-top pr-4">Function</td>
                                            <td class="whitespace-normal">{{ trace.function }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold align-top pr-4">Class</td>
                                            <td class="whitespace-normal">{{ trace.class }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <span class="italic text-xs">( and {{ logData.data.errors.stack_trace.length - 3 }} more)</span>
                            </div>
                        </div>
                    </div>

                </template>
            </DxDataGrid>
        </div>
    </MainLayout>
</template>
<script setup>
import { ref, reactive } from 'vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import {
    DxColumn,
    DxColumnChooser,
    DxDataGrid,
    DxExport,
    DxFilterRow,
    DxItem,
    DxPager,
    DxPaging,
    DxToolbar,
    DxMasterDetail,
} from 'devextreme-vue/data-grid';
import { exportDataGrid } from 'devextreme/excel_exporter';

import axios from 'axios';
import { computed } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Workbook } from 'exceljs';
import { saveAs } from 'file-saver';

// DEVEXTREME DATAGRID
const datagridRef = ref();

var logFiles = computed(() => usePage().props.log_files);
var fileSelected = ref(null);
var logData = ref([]);

function getLogData() {
    axios.get(route('user_log.detail', fileSelected.value))
        .then((response) => {
            var responseData = response.data;
            logData.value = responseData.data.log_detail;
        })
        .catch((error) => {
            var errorResponseData = error.response.data;
            ElMessage({
                message: errorResponseData.message,
                type: 'error',
            });
        });
}

function refreshDatagrid() {
    datagridRef.value.instance.refresh();
};

function onExporting(e) {
    const workbook = new Workbook();
    const worksheet = workbook.addWorksheet('Employees');
    var fileName = "data-users"

    exportDataGrid({
        component: e.component,
        worksheet,
        autoFilterEnabled: true,
    }).then(() => {
        workbook.xlsx.writeBuffer().then((buffer) => {
            saveAs(new Blob([buffer], { type: 'application/octet-stream' }), fileName + '.xlsx');
        });
    });

    e.cancel = true;
};

</script>