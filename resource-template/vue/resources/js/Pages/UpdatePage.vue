<template>
    <Head title="ResourceTitle" />
    <MainLayout title="ResourceTitle" back-confirm>
        <el-form ref="formRef" :model="formModelName" label-width="200px" label-position="top"
            require-asterisk-position="right" autocomplete="off">

            FormSlot

            <el-button type="primary" @click="submitForm">Update</el-button>
        </el-form>
    </MainLayout>
</template>
<script setup>
import { ref } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ElMessage } from 'element-plus';

const data = usePage().props.modelName;
const formRef = ref();
const formModelName = useForm({
    ...data,
});

const formErrors = ref([]);
const getFormError = (field, errors = formErrors.value) => {
    if (!errors && !errors.length) {
        return false
    }
    if (errors[field]) {
        return errors[field]
    }
}

const submitForm = async () => {
    formErrors.value = [];
    await formRef.value.validate(async (valid, _) => {
        if (valid) {
            formModelName.put(RouteUpdate, {
                preserveScroll: true,
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    router.get(RouteBrowse);
                },
                onError: (errors) => {
                    formErrors.value = errors;
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

</script>
