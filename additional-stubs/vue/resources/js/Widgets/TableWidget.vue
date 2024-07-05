<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
    <div class="flex flex-row justify-between mb-4">
        <div>
            <div class="font-bold">WidgetName</div>
            <div class="font-thin text-gray-700 text-sm">Subtitle for your widget</div>
        </div>
    </div>
    <div>
        <DxDataGrid ref="datagridRef" :data-source="dataSource" key="product_id" :column-auto-width="true"
            :remote-operations="remoteOperations" :item-per-page="10" @selection-changed="onSelectionChanged"
            @exporting="onExporting">
            <DxFilterRow :visible="true" />
            <DxExport :enabled="true" />
            <DxSelection select-all-mode="page" show-check-boxes-mode="always" mode="multiple" />
            <DxColumnChooser :enabled="true" mode="select" />
            <DxHeaderFilter :visible="true" />
            <DxPaging :page-size="10" />
            <DxPager :visible="true" :allowed-page-sizes="[10, 20, 50]" :show-page-size-selector="true" />

            <DxColumn data-field="product" caption="Product" />
            <DxColumn data-field="quantity" caption="Quantity" :dataType="'number'">
                <DxFormat type="fixedPoint" :precision="0" />
            </DxColumn>
            <DxColumn data-field="target" caption="Target" :dataType="'number'">
                <DxFormat type="fixedPoint" :precision="0" />
            </DxColumn>

            <DxToolbar>
                <DxItem location="before" template="buttonTemplate" />
                <DxItem name="columnChooserButton" />
                <DxItem name="exportButton" />
            </DxToolbar>
            <template #buttonTemplate>
                <div class="flex flex-row w-full">
                    <Transition name="fadetransition" mode="out-in" appear>
                        <div v-if="!itemSelected">
                            <BsButton type="primary" icon="arrow-path" @click="refreshDatagrid">Refresh</BsButton>
                        </div>
                        <div v-else class="h-auto flex items-center px-4">
                            <BsIconButton icon="x-mark" class="mr-2" @click="clearSelection" />
                            <span class="font-bold mr-4">{{ dataSelected.length }} dipilih</span>
                        </div>
                    </Transition>
                </div>
            </template>
        </DxDataGrid>
    </div>
</div>
</template>

<script setup>
import { defineProps, ref, computed } from 'vue';
import BsButton from '@/Components/BsButton.vue';
import {
    DxColumn,
    DxColumnChooser,
    DxDataGrid,
    DxExport,
    DxHeaderFilter,
    DxFilterRow,
    DxItem,
    DxPager,
    DxPaging,
    DxSelection,
    DxToolbar
} from 'devextreme-vue/data-grid';
import CustomStore from "devextreme/data/custom_store";
import { exportDataGrid } from 'devextreme/excel_exporter';
import { Workbook } from 'exceljs';
import { saveAs } from 'file-saver';

const props = defineProps({
    dataSource: {
        type: Array,
        // Only for dummy data, you can remove this
        default: () => {
            let data = [];
            for (let i = 0; i < 50; i++) {
                data.push({
                    product_id: i + 1,
                    product: `Product ${i + 1}`,
                    quantity: Math.floor(Math.random() * 100),
                    target: Math.floor(Math.random() * 100),
                });
            }
            return data;
        },
    }
});

// Ref and Variables
const datagridRef = ref();
const allMode = ref("page");
const dataGridAction = ref("index");
const btnEditVisible = ref(false);
const btnDeleteVisible = ref(false);
const dataSelected = ref([]);
var itemSelected = computed(() => dataSelected.value.length > 0);

// Remote Operations
const remoteOperations = ref({
    paging: true,
    filtering: true,
    sorting: true,
});

function isNotEmpty(value) {
    return value !== undefined && value !== null && value !== "";
};

// ========================================================================
// If you want to use server side processing
// ========================================================================
// const dataSource = new CustomStore({
//     key: "user_id",
//     load: function (loadOptions) {
//         let params = "?";
//         ["skip", "take", "requireTotalCount", "sort", "filter"].forEach(
//             function (i) {
//                 if (i in loadOptions && isNotEmpty(loadOptions[i])) {
//                     params += `${i}=${JSON.stringify(loadOptions[i])}&`;
//                 }
//             }
//         );
//         params = params.slice(0, -1);

//         if (dataGridAction.value == "select.all") {
//             if (allMode.value == "allPages") {
//                 return axios.get(route('user.data_processing'), { params: params })
//                     .then((response) => {
//                         dataGridAction.value = "index";
//                         data = response.data;
//                     })
//                     .catch((error) => { });
//             } else {
//                 dataGridAction.value = "index";
//             }
//         } else {
//             return axios.get(route('user.data_processing') + params)
//                 .then((response) => {
//                     dataGridAction.value = "index";
//                     return response.data;
//                 })
//                 .catch((error) => { });
//         }
//     }.bind(this),
// });

// On Refresh Datagrid
function refreshDatagrid() {
    datagridRef.value.instance.refresh();
};

// On Selection Changed
function onSelectionChanged(data) {
    dataSelected.value = data.selectedRowsData;

    if (data.selectedRowKeys.length < 1) {
        btnEditVisible.value = false;
        btnDeleteVisible.value = false;
    } else if (data.selectedRowKeys.length == 1) {
        btnEditVisible.value = true;
        btnDeleteVisible.value = true;
    } else {
        btnEditVisible.value = false;
        btnDeleteVisible.value = false;
    }
};

// On Exporting
function onExporting(e) {
    const workbook = new Workbook();
    const worksheet = workbook.addWorksheet('WidgetName');
    var fileName = "data-WidgetName"

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

// Clear Selection
function clearSelection() {
    const dataGrid = datagridRef.value.instance;
    dataGrid.clearSelection();
    dataSelected.value = [];
}

</script>
