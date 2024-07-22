<template>
    <Head title="User Management" />
    <MainLayout title="User Management">
        <template #header-action>
            <BsButton type="primary" icon="plus" @click="addUserAction" v-if="can('user.create')">Add User</BsButton>
            <BsButton type="primary" icon="arrows-up-down" @click="syncLeader" v-if="btnSyncLeaderVisible && can('user.update')">Sync Leader</BsButton>
        </template>
        <div class="flex flex-col">
            <DxDataGrid ref="datagridRef" :data-source="dataSource" key="user_id" :column-auto-width="true"
                :remote-operations="remoteOperations" :item-per-page="10" @selection-changed="onSelectionChanged"
                :hover-state-enabled="true" @cell-dbl-click="editUserAction($event.data)" @exporting="onExporting">
                <DxFilterRow :visible="true" />
                <DxExport :enabled="true" />
                <DxSelection select-all-mode="page" show-check-boxes-mode="always" mode="multiple" />
                <DxColumnChooser :enabled="true" mode="select" />
                <DxHeaderFilter :visible="true" />
                <DxPaging :page-size="10" />
                <DxPager :visible="true" :allowed-page-sizes="[10, 20, 50]" :show-page-size-selector="true" />
                <DxColumn data-field="profile_picture" caption="Profile" :allowHeaderFiltering="false" width="100" alignment="center" cell-template="profile" :allowExporting="false" />
                <template #profile="{ data }">
                    <el-image
                        :src="route('account-picture', {npk: data.data?.npk || 'default'})"
                        fit="cover"
                        class="rounded-full"
                        style="width: 50px; height: 50px;"
                        :preview-src-list="[route('account-picture', {npk: (data.data?.npk || 'default')})]"
                    />
                </template>
                <DxColumn data-field="username" caption="Username" :allowHeaderFiltering="false" />
                <DxColumn data-field="npk" caption="NPK" :allowHeaderFiltering="false" />
                <DxColumn data-field="name" caption="Nama" :allowHeaderFiltering="false" />
                <DxColumn data-field="email" caption="Email" :allowHeaderFiltering="false" />
                <DxColumn caption="Role" cell-template="role" width="200" :allowExporting="false" />
                <template #role="{ data }">
                    <div class="flex flex-row justify-start items-center" v-if="data.data.roles.length > 0">
                        <div class="bg-primary rounded-full px-3 py-1 text-white m-px w-min text-xs">
                            {{ data.data.roles[0].name }}
                        </div>

                        <el-popover placement="top" :width="150" trigger="hover" v-if="data.data.roles.length > 1">
                            <template #reference>
                                <div
                                    class=" bg-primary-hover rounded-full px-2 py-1 text-white m-px w-min text-xs cursor-pointer">
                                    + {{ data.data.roles.length - 1 }}
                                </div>
                            </template>
                            <template #default>
                                <div class="w-full flex flex-col justify-center items-center">
                                    <div class="bg-primary rounded-full px-3 py-1 text-white m-px text-xs w-fit"
                                        v-for="role in data.data.roles.slice(1, data.data.roles.length)">
                                        {{ role.name }}
                                    </div>
                                </div>
                            </template>
                        </el-popover>
                    </div>
                </template>
                <DxColumn data-field="is_active" caption="Status" cell-template="user-status" width="110" alignment="center" :allowFiltering="true" :allowHeaderFiltering="false" data-type="boolean" false-text="Inactive" true-text="Active" :filter-values="[0, 1]"/>
                <template #user-status="{ data }">
                    <span v-if="data.data.is_active"
                        class="px-4 py-2 rounded-md bg-success text-white text-xs">Active</span>
                    <span v-else class="px-4 py-2 rounded-md bg-danger text-white text-xs">Inactive</span>
                </template>
                <DxColumn cell-template="action" width="60" alignment="center" :allowExporting="false"
                    :showInColumnChooser="false" />
                <template #action="{ data }">
                    <el-dropdown trigger="click" placement="bottom-end" :disabled="!can('user.update|user.delete')">
                        <span class="el-dropdown-link">
                            <BsIcon icon="ellipsis-vertical" />
                        </span>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item @click="editUserAction(data.data)" v-if="can('user.update')">
                                    <BsIcon icon="pencil-square" class="mr-2" /> Edit User
                                </el-dropdown-item>
                                <el-dropdown-item v-if="!data.data.is_active && can('user.update')"
                                    @click="switchUserStatus(data.data, true)">
                                    <BsIcon icon="arrow-path-rounded-square" class="mr-2" /> Enable User
                                </el-dropdown-item>
                                <el-dropdown-item v-else-if="can('user.update')"
                                    @click="switchUserStatus(data.data, false)">
                                    <BsIcon icon="arrow-path-rounded-square" class="mr-2" /> Disable User
                                </el-dropdown-item>
                                <el-dropdown-item v-if="can('user.delete')" @click="deleteUserAction(data.data)">
                                    <BsIcon icon="trash" class="mr-2" /> Delete User
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
                                <!-- Table Header Action Here -->
                            </div>
                            <div v-else class="flex items-center border-2 border-primary-border rounded-full gap-1 text-sm">
                                <BsIconButton icon="x-mark" @click="clearSelection" />
                                <span class="font-bold mr-2">{{ dataSelected.length }} dipilih</span>

                                <div class="flex items-center border-l-2 px-2 h-full gap-1">
                                    <div class="flex items-center rounded-full hover:bg-gray-200 cursor-pointer" @click="switchUserStatus(dataSelected, true)"  v-if="can('user.update')">
                                        <BsIconButton icon="check-circle" class="text-success" />
                                        <span class="mr-2 font-semibold">Enable</span>
                                    </div>
                                    <div class="flex items-center rounded-full hover:bg-gray-200 cursor-pointer" @click="switchUserStatus(dataSelected, false)"  v-if="can('user.update')">
                                        <BsIconButton icon="x-circle" class="text-danger" />
                                        <span class="mr-2 font-semibold">Disable</span>
                                    </div>
                                    <p class="font-semibold italic text-gray-700" v-if="!can('user.update')">No Action</p>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </template>
            </DxDataGrid>
        </div>
        <el-dialog v-model="dialogFormVisible" width="500px" :append-to-body="true" :destroy-on-close="true"
            class="!rounded-xl">
            <template #header>
                <span class="font-bold text-lg">{{ !editMode ? 'Create' : 'Edit' }} User</span>
            </template>
            <el-form ref="formUserRef" :model="formUser" label-width="200px" label-position="top"
                require-asterisk-position="right" autocomplete="off">
                <el-form-item :error="getFormError('username')" prop="username" label="Username" :required="true">
                    <el-input v-model="formUser.username" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormError('name')" prop="name" label="Nama" :required="true">
                    <el-input v-model="formUser.name" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormError('npk')" prop="npk" label="NPK">
                    <el-input v-model="formUser.npk" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormError('email')" prop="email" label="Email">
                    <el-input type="email" v-model="formUser.email" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormError('password')" prop="password" label="Password" :required="true" v-if="!editMode">
                    <el-input type="password" v-model="formUser.password" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormError('role')" props="role" label="Role">
                    <el-select v-model="formUser.role" multiple placeholder="Select" class="w-full">
                        <el-option v-for="role in roles" :key="role.id" :label="role.name" :value="role.id" />
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer flex">
                    <el-button class=" flex-grow" @click="closeDialog">Cancel</el-button>
                    <el-button class=" flex-grow" v-if="!editMode" type="primary"
                        @click="addUserSubmitAction">Submit</el-button>
                    <el-button class=" flex-grow" v-if="editMode" type="primary"
                        @click="editUserSubmitAction">Update</el-button>
                </span>
            </template>
        </el-dialog>
    </MainLayout>
</template>
<script setup>
import { ref, reactive } from 'vue';
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
import { ElLoading } from 'element-plus';
import { dxLoad } from '@/Core/Helpers/dx-helpers';

// DIALOG FORM
const formUserRef = ref();
const dialogFormVisible = ref(false);
const editMode = ref(false);
const roles = computed(() => usePage().props.roles);
const btnSyncLeaderVisible = computed(()=>usePage().props.leader_enabled);

const formUser = useForm({
    user_id: '',
    user_uuid: '',
    username: '',
    name: '',
    npk: '',
    email: '',
    password: '',
    role: [],
});
const formUserErrors = ref([]);

function getFormError(field, errors = formUserErrors.value) {
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
function addUserAction() {
    editMode.value = false;
    dialogFormVisible.value = true;

    formUser.user_uuid = '';
    formUser.user_id = '';
    formUser.username = '';
    formUser.name = '';
    formUser.npk = '';
    formUser.email = '';
    formUser.password = '';
    formUser.role = [];
}
async function addUserSubmitAction() {
    await formUserRef.value.validate((valid, _) => {
        if (valid) {
            formUser.post(route('user.create'), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    formUserErrors.value = [];
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formUserErrors.value = errors;
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
function editUserAction(dataUser) {
    editMode.value = true;
    dialogFormVisible.value = true;

    formUser.user_id = dataUser.user_id;
    formUser.user_uuid = dataUser.user_uuid;
    formUser.username = dataUser.username;
    formUser.name = dataUser.name;
    formUser.npk = dataUser.npk;
    formUser.email = dataUser.email;
    formUser.password = '';
    formUser.role = dataUser.roles.map(role => role.id);
}
async function editUserSubmitAction() {
    await formUserRef.value.validate(async (valid, _) => {
        if (valid) {
            formUser.put(route('user.update',formUser.user_uuid), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    formUserErrors.value = [];
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formUserErrors.value = errors;
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
function deleteUserAction(dataUser) {
    ElMessageBox.confirm(
        'Are you sure to delete this user ?',
        'Warning',
        {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
        }
    )
        .then(() => {
            router.delete(route('user.delete',dataUser.user_uuid), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formUserErrors.value = errors;
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
function switchUserStatus(dataUser, status) {
    if (Array.isArray(dataUser)) {
        ElMessageBox.confirm(
            'Are you sure to switch these users status to ' + (status ? 'Active' : 'Inactive') + ' ?',
            'Warning',
            {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning',
            }
        ).then(() => {
            dataUser.forEach((user) => {
                switchUserStatus(user, status);
            });
        }).catch(() => {
            ElMessage({
                type: 'info',
                message: 'Action Canceled',
            })
        });
    } else {
        useForm({
            is_active : status
        }).put(route('user.switch_status', dataUser.user_uuid), {
            onSuccess: (response) => {
                ElMessage({
                    message: response.props.flash.message,
                    type: 'success',
                });
                refreshDatagrid();
                dialogFormVisible.value = false;
            },
            onError: (errors) => {
                formUserErrors.value = errors;
            },
        });
    }
}
function syncLeader(){
    const loading = ElLoading.service({
        lock: true,
        text: "Sync PKT Leader ...",
    });
    formUser.post(route('user.sync_leader'), {
        onSuccess: (response) => {
            ElMessage({
                message: response.props.flash.message,
                type: 'success',
            });
            refreshDatagrid();
            loading.close();

        },
        onError: (errors) => {
            if('message' in errors){
                ElMessage({
                    message: errors.message,
                    type: 'error',
                });
            }
            loading.close();
        }
    });
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
const dataKey = 'user_id'; //change to data primary key
const dataRoute = route('user.data_processing') //change to data processing route
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

function clearSelection() {
    const dataGrid = datagridRef.value.instance;
    dataGrid.clearSelection();
    dataSelected.value = [];
}

</script>