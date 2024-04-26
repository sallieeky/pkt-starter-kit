<template>
    <Head title="ResourceTitle" />
    <MainLayout title="ResourceTitle">
        <div class="flex flex-col">
            <DxDataGrid ref="datagridRef" :data-source="dataSource" key-expr="PrimaryKey" :column-auto-width="true"
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
                </DxToolbar>
                <template #buttonTemplate>
                    <div class="flex flex-row w-full">
                        <Transition name="fadetransition" mode="out-in" appear>
                            <div v-if="!itemSelected">
                                <BsButton type="primary" icon="plus" @click="addModelNameAction" v-if="CreatePermission">
                                    Add ModelLabel</BsButton>
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
        'Apakah anda yakin untuk mengahapus ModelLabel ini ?',
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
const allMode = ref("page");
const dataGridAction = ref("index");
const btnEditVisible = ref(false);
const btnDeleteVisible = ref(false);
const dataSelected = ref([]);

var itemSelected = computed(() => dataSelected.value.length > 0);

const remoteOperations = ref({
    paging: true,
    filtering: true,
    sorting: true,
});

function isNotEmpty(value) {
    return value !== undefined && value !== null && value !== "";
};

const dataSource = new CustomStore({
    key: "PrimaryKey",
    load: function (loadOptions) {
        let params = "?";
        ["skip", "take", "requireTotalCount", "sort", "filter"].forEach(
            function (i) {
                if (i in loadOptions && isNotEmpty(loadOptions[i])) {
                    params += `${i}=${JSON.stringify(loadOptions[i])}&`;
                }
            }
        );
        params = params.slice(0, -1);

        if (dataGridAction.value == "select.all") {
            if (allMode.value == "allPages") {
                return axios.get(RouteDataProcessing, { params: params })
                    .then((response) => {
                        dataGridAction.value = "index";
                        data = response.data;
                    })
                    .catch((error) => { });
            } else {
                dataGridAction.value = "index";
            }
        } else {
            return axios.get(RouteDataProcessing + params)
                .then((response) => {
                    dataGridAction.value = "index";
                    return response.data;
                })
                .catch((error) => { });
        }
    }.bind(this),
});

function refreshDatagrid() {
    datagridRef.value.instance.refresh();
};

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
