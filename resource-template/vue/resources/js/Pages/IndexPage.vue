<template>
    <Head title="ResourceTitle" />
    <MainLayout title="ResourceTitle">
        <template #header-action>
            <BsButton type="primary" icon="plus" @click="addModelNameAction" v-if="CreatePermission">Add ModelLabel</BsButton>
        </template>
        <div class="flex flex-col">
            <DxDataGrid ref="datagridRef" :data-source="dataSource" key="PrimaryKey" :column-auto-width="true"
                :remote-operations="remoteOperations" :item-per-page="10" @selection-changed="onSelectionChanged"
                @exporting="onExporting">
                <DxFilterRow :visible="true" />
                <DxExport :enabled="true" />
                <DxSelection select-all-mode="page" show-check-boxes-mode="always" mode="multiple" />
                <DxColumnChooser :enabled="true" mode="select" />
                <DxHeaderFilter :visible="true" />
                <DxPaging :page-size="10" />
                <DxPager :visible="true" :allowed-page-sizes="[10, 20, 50]" :show-page-size-selector="true" />

                ColumnTableSlot

                <DxColumn cell-template="action" width="60" alignment="center" :allowExporting="false"
                    :showInColumnChooser="false" />
                <template #action="{ data }">
                    <el-dropdown trigger="click" placement="bottom-end" :disabled="!ActionPermission">
                        <span class="el-dropdown-link">
                            <BsIcon icon="ellipsis-vertical" />
                        </span>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item @click="editModelNameAction(data.data)" v-if="UpdatePermission">
                                    <BsIcon icon="pencil-square" class="mr-2" /> Edit ModelLabel
                                </el-dropdown-item>
                                <el-dropdown-item v-if="DeletePermission" @click="deleteModelNameAction(data.data)">
                                    <BsIcon icon="trash" class="mr-2" /> Delete ModelLabel
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </template>

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

                                <div class="flex items-center border-l-2 px-2 h-full gap-1">
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

        <!-- MODAL CREATE & EDIT -->
        <el-dialog v-model="dialogFormVisible" width="500px" :append-to-body="true" :destroy-on-close="true"
            class="!rounded-xl">
            <template #header>
                <span class="font-bold text-lg">{{ !editMode ? 'Create' : 'Edit' }} ModelLabel</span>
            </template>
            <el-form ref="formModelNameRef" :model="formModelName" label-width="200px" label-position="top"
                require-asterisk-position="right" autocomplete="off">

                ModalFormSlot

            </el-form>
            <template #footer>
                <span class="dialog-footer flex">
                    <el-button class=" flex-grow" @click="closeDialog">Cancel</el-button>
                    <el-button class=" flex-grow" v-if="!editMode" type="primary"
                        @click="addModelNameSubmitAction">Submit</el-button>
                    <el-button class=" flex-grow" v-if="editMode" type="primary"
                        @click="editModelNameSubmitAction">Update</el-button>
                </span>
            </template>
        </el-dialog>

    </MainLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { can } from '@/Core/Helpers/permission-check';
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
import { exportDataGrid } from 'devextreme/excel_exporter';

import CustomStore from "devextreme/data/custom_store";
import axios from 'axios';
import { computed } from 'vue';
import BsButton from '@/Components/BsButton.vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Workbook } from 'exceljs';
import { saveAs } from 'file-saver';
import BsIcon from '@/Components/BsIcon.vue';
import BsIconButton from '@/Components/BsIconButton.vue';
import { dxLoad } from '@/Core/Helpers/dx-helpers';

// DIALOG FORM
const formModelNameRef = ref();
const dialogFormVisible = ref(false);
const editMode = ref(false);

const formModelName = useForm({
    FormUseForm
});
const formModelNameErrors = ref([]);

function getFormError(field, errors = formModelNameErrors.value) {
    if (!errors && !errors.length) {
        return false
    }
    if (errors[field]) {
        return errors[field]
    }
}

function closeDialog() {
    dialogFormVisible.value = false;
}
function addModelNameAction() {
    editMode.value = false;
    dialogFormVisible.value = true;

    FormAddAction
}
async function addModelNameSubmitAction() {
    await formModelNameRef.value.validate((valid, _) => {
        if (valid) {
            formModelName.post(RouteCreate, {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    formModelNameErrors.value = [];
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formModelNameErrors.value = errors;
                    if('message' in errors){
                        ElMessage({
                            message: errors.message,
                            type: 'error',
                        });
                    }
                }
            });
        }
    });
}
function editModelNameAction(dataModelName) {
    editMode.value = true;
    dialogFormVisible.value = true;

    FormEditAction
}
async function editModelNameSubmitAction() {
    await formModelNameRef.value.validate(async (valid, _) => {
        if (valid) {
            formModelName.put(RouteUpdate, {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    formModelNameErrors.value = [];
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formModelNameErrors.value = errors;
                    if('message' in errors){
                        ElMessage({
                            message: errors.message,
                            type: 'error',
                        });
                    }
                }
            });
        }
    });
}
function deleteModelNameAction(dataModelName) {
    ElMessageBox.confirm(
        'Are you sure to delete this ModelLabel ?',
        'Warning',
        {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
        }
    )
        .then(() => {
            router.delete(RouteDelete, {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formModelNameErrors.value = errors;
                }
            });
        })
        .catch(() => {
            ElMessage({
                type: 'info',
                message: 'Action Canceled',
            })
        })
}

// DEVEXTREME DATAGRID
const datagridRef = ref();
const dataSelected = ref([]);

var itemSelected = computed(() => dataSelected.value.length > 0);

const remoteOperations = ref({
    paging: true,
    filtering: true,
    sorting: true,
});

const dataKey = 'PrimaryKey'; //change to data primary key
const dataRoute = RouteDataProcessing;
const dataSource = new CustomStore({
    key: dataKey,
    load: dxLoad(dataRoute).bind(this),
});

function refreshDatagrid() {
    datagridRef.value.instance.refresh();
};

function onSelectionChanged(data) {
    dataSelected.value = data.selectedRowsData;
};

function onExporting(e) {
    const workbook = new Workbook();
    const worksheet = workbook.addWorksheet('ModelName');
    var fileName = "data-ModelName"

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

function clearSelection() {
    const dataGrid = datagridRef.value.instance;
    dataGrid.clearSelection();
    dataSelected.value = [];
}

</script>
