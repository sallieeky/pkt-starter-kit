<template>
    <Head title="PageTitle" />
    <MainLayout title="PageTitle">
        <div>
            <el-form ref="formRef" :model="form" label-width="200px" label-position="top"
                require-asterisk-position="right" autocomplete="off">

                    <!-- Your form here -->
                    <el-form-item :error="getFormError('name')" prop="name" label="Name" :required="true">
                        <el-input v-model="form.name" autocomplete="one-time-code" autocorrect="off" spellcheck="false" class="!w-full" />
                    </el-form-item>
                    <el-form-item :error="getFormError('position')" prop="position" label="Position" :required="true">
                        <el-select v-model="form.position" placeholder="Select position" class="!w-full">
                            <el-option label="Option 1" value="1" />
                            <el-option label="Option 2" value="2" />
                            <el-option label="Option 3" value="3" />
                        </el-select>
                    </el-form-item>
                    <!-- End your form here -->

                <el-button type="primary" @click="submitForm">Submit</el-button>
            </el-form>
        </div>
    </MainLayout>
</template>
<script setup>
import { ref } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { ElMessage } from 'element-plus';

// ============================================
// You can remove this dummy data
// ============================================
const form = useForm({
    id: null,
    name: null,
    position: null,
});

const formRef = ref();
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
            // Set the route to the form action
            form.post(route('...'), {
                preserveScroll: true,
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
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