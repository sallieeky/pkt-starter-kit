<template>
    <Head title="Role & Permission" />
    <MainLayout title="Role & Permission">
        <div class="flex">
            <div class="w-80 mr-4 flex-flex-col flex-grow-0 flex-shrink-0">
                <div class="flex justify-end mb-4">
                    <el-input placeholder="Search" v-model="search">
                        <template #prefix>
                            <BsIcon icon="magnifying-glass"></BsIcon>
                        </template>
                    </el-input>
                    <BsIconButton icon="plus" @click="addUserRoleAction" v-if="can('role.create')"></BsIconButton>
                </div>
                <div class="h-[500px] overflow-y-scroll scroll pr-2">
                    <div v-if="isRolesEmpty" class="h-full w-full flex items-center justify-center">
                        <span class="text-gray-700 italic">
                            No Data
                        </span>
                    </div>
                    <div v-else>
                        <div
                            class="p-4 rounded-xl border border-gray-400  w-full mb-2 cursor-pointer"
                            :class="[
                                {'bg-primary' : role.id == idSelectedRole},
                                {'bg-white hover:bg-primary-surface' : role.id != idSelectedRole},
                            ]"
                            v-for="role in filteredRoles" :key="role.id" @click="selectingRole(role)">
                            <div class="flex flex-row justify-between items-center">
                                <div class="flex flex-row items-center">
                                    <div class="flex flex-col items-start">
                                        <span class="font-bold text-left" 
                                            :class="[
                                                {'text-white' : role.id == idSelectedRole},
                                                {'text-black' : role.id != idSelectedRole},
                                            ]">
                                            {{ role.name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grow">
                <div class="col-span-12 lg:col-span-4 p-4 flex-1 bg-primary-surface rounded-xl">
                    <div class="h-[500px] w-full flex items-center justify-center flex-col" v-if="!isAnyRoleSelected">
                        <BsIcon icon="exclamation-triangle" size="48" class=" text-secondary"></BsIcon>
                        <h4 class="text-xl font-bold mb-2">No User Role Selected</h4>
                        <span class="w-56 text-center text-gray-700">
                            Select a role from the left to view or edit the user roles.
                        </span>
                    </div>
                    <div v-else>
                        <Transition name="fadetransition" mode="out-in" appear>
                            <div class="h-[500px] w-full flex items-center justify-center flex-col" v-if="permissionLoading">
                                <BsLoading size="100"/>
                            </div>
                            <div v-else class="flex flex-col">
                                <div class="flex flex-row justify-between items-center mb-4">
                                    <div class="flex flex-col">
                                        <h3 class="text-xl font-bold">{{ selectedRole.name }}</h3>
                                        <span class="text-sm text-gray-800">{{ totalPermissionGranted }} Permission Granted | {{ totalUser }} Users</span>
                                    </div>
                                    <div>
                                        <el-dropdown trigger="click" placement="bottom-end" :disabled="!can('role.update|role.delete')">
                                            <span class="el-dropdown-link">
                                                <BsIcon icon="ellipsis-vertical" />
                                            </span>
                                            <template #dropdown>
                                                <el-dropdown-menu>
                                                    <el-dropdown-item @click="editUserRoleAction(selectedRole)" v-if="can('role.update')">
                                                        <BsIcon icon="pencil-square" class="mr-2" /> Rename
                                                    </el-dropdown-item>
                                                    <el-dropdown-item @click="deleteUserRoleAction(selectedRole)" v-if="can('user.delete')">
                                                        <BsIcon icon="trash" class="mr-2" /> Delete
                                                    </el-dropdown-item>
                                                </el-dropdown-menu>
                                            </template>
                                        </el-dropdown>
                                    </div>
                                </div>
                                <el-tabs v-model="activeTab" class="demo-tabs" @tab-click="handleClick">
                                    <el-tab-pane label="Permission" name="permission">
                                        <div class="grid grid-cols-1 xl:grid-cols-2">
                                            <div class="bg-white rounded-lg p-4 mx-2 mb-2 shadow-md" v-for="permissionList,permissionGroupName in rolePermissions">
                                                <h5 class="text-md font-bold">{{ parsePermissionName(permissionGroupName) }}</h5>
                                                <el-divider />
                                                <div class="grid grid-cols-1 2xl:grid-cols-2">
                                                    <div class="flex flex-row justify-between items-center px-4 py-2" v-for="permissionObj in permissionList">
                                                        <span class="text-gray-800">{{ parsePermissionName(permissionObj.name) }}</span>
                                                        <el-switch :disabled="!can('role.assign_permission')" :active-value="1" :inactive-value="0" v-model="permissionObj.role_has_permission" @change="(newValue)=>onSwitchChange(selectedRole.id, permissionObj, newValue)"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </el-tab-pane>
                                    <el-tab-pane label="User" name="user">
                                        <div v-for="user in roleUsers">
                                            <div class="shrink-0 rounded-lg py-2 px-5 border-2 border-[#f1f4f6] flex align-middle items-center bg-white group-hover:bg-primary-surface cursor-pointer mt-2">
                                                <div class="bg-[#e4e4e5] p-4 w-10 h-10 rounded-full text-gray-800 font-bold flex items-center justify-center mr-2">
                                                    {{ user.name.charAt(0).toUpperCase() }}
                                                </div>
                                                <div class="flex items-center">
                                                    <div class=" flex flex-col text-gray-900">
                                                        <div class="w-32 truncate text-md font-bold">{{ user.name }}</div>
                                                        <div class="w-32 truncate text-xs font-thin">{{ user.username }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </el-tab-pane>
                                </el-tabs>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </div>
        <el-dialog v-model="dialogFormRoleVisible" width="500px" :append-to-body="true" :destroy-on-close="true"
            class="!rounded-xl">
            <template #header>
                <span class="font-bold text-lg">{{ !editMode ? 'Tambah' : 'Edit' }} User Role</span>
            </template>
            <el-form ref="formUserRoleRef" :model="formUserRole" label-width="200px" label-position="top"
                require-asterisk-position="right" autocomplete="off">
                <el-form-item prop="name" label="Nama" :required="true">
                    <el-input v-model="formUserRole.name" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" :maxlength="15" :show-word-limit="true"/>
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer flex">
                    <el-button class=" flex-grow" @click="closeDialog">Cancel</el-button>
                    <el-button class=" flex-grow" v-if="!editMode" type="primary"
                        @click="addUserRoleSubmitAction">Submit</el-button>
                    <el-button class=" flex-grow" v-if="editMode" type="primary"
                        @click="editUserRoleSubmitAction">Update</el-button>
                </span>
            </template>
        </el-dialog>
    </MainLayout>
</template>
<script setup>
import BsIconButton from '@/Components/BsIconButton.vue';
import BsIcon from '@/Components/BsIcon.vue';
import BsLoading from '@/Components/BsLoading.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, usePage, router, useForm } from '@inertiajs/vue3';
import { ref, computed, reactive } from 'vue';
import { ElMessage, ElMessageBox } from 'element-plus';
import { can } from '@/Core/Helpers/permission-check';

// CRUD user role
const dialogFormRoleVisible = ref(false);
const editMode = ref(false);
const formUserRoleRef = ref();
const search = ref('');
const formUserRole = useForm({
    id: '',
    name: '',
});
const roles = computed(() => usePage().props.roles);
const filteredRoles = computed(()=>{
    return roles.value.filter(role => role.name.toLowerCase().includes(search.value.toLowerCase()));
});
const isRolesEmpty = computed(() => filteredRoles.value.length < 1);

function closeDialog() {
    dialogFormRoleVisible.value = false;
}

function addUserRoleAction() {
    dialogFormRoleVisible.value = true;
    editMode.value = false;

    formUserRole.id = null;
    formUserRole.name = '';
}
async function addUserRoleSubmitAction() {
    await formUserRoleRef.value.validate((valid, _) => {
        if (valid) {
            dialogFormRoleVisible.value = false;
            formUserRole.post(route('role.create'), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    router.reload({ only: ['roles'] });
                },
                onError: (errors) => {
                    ElMessage({
                        message: 'Something wrong !!!',
                        type: 'error',
                    });
                }
            });
        }
    });
}
function editUserRoleAction(dataRole) {
    dialogFormRoleVisible.value = true;
    editMode.value = true;

    formUserRole.id = dataRole.id;
    formUserRole.name = dataRole.name;
}
async function editUserRoleSubmitAction() {
    await formUserRoleRef.value.validate(async (valid, _) => {
        if (valid) {
            dialogFormRoleVisible.value = false;
            formUserRole.put(route('role.update',formUserRole.id), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    router.reload({ only: ['roles'] });
                },
                onError: (errors) => {
                    ElMessage({
                        message: 'Something wrong !!!',
                        type: 'error',
                    });
                }
            });
        }
    });
}
function deleteUserRoleAction(dataRole) {
    ElMessageBox.confirm(
        'Apakah anda yakin untuk mengahapus user ini ?',
        'Warning',
        {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
        }
    )
        .then(() => {
            router.delete(route('role.delete',dataRole.id), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    idSelectedRole.value = null;
                    router.reload({ only: ['roles'] });
                },
                onError: (errors) => {
                    ElMessage({
                        message: 'Something wrong !!!',
                        type: 'error',
                    });
                }
            });
        })
        .catch(() => {
            ElMessage({
                type: 'info',
                message: 'Action Canceled',
            })
        });
}

// Role permission & User
const activeTab = ref('permission');
const idSelectedRole = ref(null);
const rolePermissions = ref(null);
const roleUsers = ref(null);
const totalPermissionGranted = ref(0);
const totalUser = ref(0);
const selectedRole = computed(()=>roles.value.filter(role => role.id == idSelectedRole.value)[0]);
const isAnyRoleSelected = computed(()=>idSelectedRole.value!=null);
const permissionLoading = ref(false);

function selectingRole(dataRole){
    idSelectedRole.value = dataRole.id;
    permissionLoading.value = true;
    axios.get(route('role.permission_list',dataRole.id))
        .then((response) => {
            var responseData = response.data;
            rolePermissions.value = responseData.data.permissions;
            totalPermissionGranted.value = responseData.data.total_assigned_permission;
        })
        .catch((error) => {
            var errorResponseData = error.response.data;
            ElMessage({
                message: errorResponseData.message,
                type: 'error',
            });
        })
        .finally(()=>{
            setTimeout(() => {
                permissionLoading.value = false;
            }, 500)
        });
    axios.get(route('role.user_list',dataRole.id))
        .then((response) => {
            var responseData = response.data;
            roleUsers.value = responseData.data.users;
            totalUser.value = responseData.data.user_count;
        })
        .catch((error) => {
            var errorResponseData = error.response.data;
            ElMessage({
                message: errorResponseData.message,
                type: 'error',
            });
        })
        .finally(()=>{
            setTimeout(() => {
                permissionLoading.value = false;
            }, 500)
        });
}
function parsePermissionName(str){
    let text = str.split('.').slice(-1)[0];
    let words = text.split('_');
    let result = words.map(word => word.charAt(0).toLowerCase() + word.slice(1)).join(' ');
    result = result.charAt(0).toUpperCase() + result.slice(1);
    return result;
}
function onSwitchChange(idRole, permissionData, newValue){
    const formData = {
        id_permission : permissionData.id,
        permission_name : permissionData.name,
        value : newValue == 1 ? true : false,
    };
    return new Promise((resolve, reject) => {
        return axios.put(route('role.switch_permission',idRole),formData)
            .then((response) => {
                var responseData = response.data;
                totalPermissionGranted.value = newValue == 0 ? totalPermissionGranted.value - 1 : totalPermissionGranted.value + 1;
                ElMessage({
                    message: responseData.message,
                    grouping: true,
                    type: 'success',
                });
                return resolve(true);
            })
            .catch((error) => {
                var errorResponseData = error.response.data;
                permissionData.role_has_permission = newValue == 0 ? 1 : 0;
                ElMessage({
                    message: errorResponseData.message,
                    grouping: true,
                    type: 'error',
                });
                return reject(new Error('error'));
            });
        });
}

</script>