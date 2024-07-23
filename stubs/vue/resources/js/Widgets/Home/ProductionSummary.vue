<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
    <div class="flex flex-row justify-between mb-4">
        <div>
            <div class="font-bold">Production Summary</div>
            <div class="font-thin text-gray-700 text-sm">Summary of production data</div>
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

            <!-- Your column here -->
            <DxColumn data-field="product" caption="Product" />
            <DxColumn data-field="quantity" caption="Quantity" :dataType="'number'">
                <DxFormat type="fixedPoint" :precision="0" />
            </DxColumn>
            <DxColumn data-field="target" caption="Target" :dataType="'number'">
                <DxFormat type="fixedPoint" :precision="0" />
            </DxColumn>
            <!-- End your column here -->

            <DxToolbar>
                <DxItem location="before" template="buttonTemplate" />
                <DxItem name="columnChooserButton" />
                <DxItem name="exportButton" />
                <DxItem widget="dxButton" :options="{ icon: 'refresh', onClick: refreshDatagrid }" />
            </DxToolbar>
            <template #buttonTemplate>
                <div class="flex w-full">
                    <Transition name="fadetransition" mode="out-in" appear>
                        <div v-if="!itemSelected">
                            <!-- Table Action Here -->
                        </div>
                        <div v-else class="flex items-center border-2 border-primary-border rounded-full gap-1 text-sm">
                            <BsIconButton icon="x-mark" @click="clearSelection" />
                            <span class="font-bold mr-2">{{ dataSelected.length }} dipilih</span>

                            <div class="flex items-center border-l-2 px-2 h-full">
                                <!-- Table Bulk Action -->
                                <p class="font-semibold italic text-gray-700">No Action</p>
                                <!-- End Table Bulk Action -->
                            </div>
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
import BsIconButton from '@/Components/BsIconButton.vue';
import {
    DxColumn,
    DxColumnChooser,
    DxDataGrid,
    DxExport,
    DxFormat,
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
import { dxLoad } from '@/Core/Helpers/dx-helpers';

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
const dataSelected = ref([]);
var itemSelected = computed(() => dataSelected.value.length > 0);

// Remote Operations
const remoteOperations = ref({
    paging: true,
    filtering: true,
    sorting: true,
});

// ========================================================================
// If you want to use server side processing
// ========================================================================
// const dataKey = 'user_id'; //change to data primary key
// const dataRoute = route('user.data_processing') //change to data processing route
// const dataSource = new CustomStore({
//     key: dataKey,
//     load: dxLoad(dataRoute).bind(this),
// });

// On Refresh Datagrid
function refreshDatagrid() {
    datagridRef.value.instance.refresh();
};

// On Selection Changed
function onSelectionChanged(data) {
    dataSelected.value = data.selectedRowsData;
};

// On Exporting
function onExporting(e) {
    const workbook = new Workbook();
    const worksheet = workbook.addWorksheet('Production Summary');
    var fileName = "data-Production Summary"

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
