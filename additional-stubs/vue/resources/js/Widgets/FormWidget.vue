<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
        <div class="flex flex-row justify-between mb-4">
            <div>
                <div class="font-bold">WidgetName</div>
                <div class="font-thin text-gray-700 text-sm">Subtitle for your widget</div>
            </div>
        </div>
        <div>
            <el-form ref="formRef" :model="form" label-width="200px" label-position="top"
                require-asterisk-position="right" autocomplete="off">

                    <!-- Your form here -->
                    <el-form-item :error="getFormError('name')" prop="name" label="Name" :required="true">
                        <el-input v-model="form.name" autocomplete="one-time-code" autocorrect="off" spellcheck="false" class="!w-full" />
                    </el-form-item>
                    <el-form-item :error="getFormError('position')" prop="position" label="Position" :required="true">
                        <el-select v-model="form.position" placeholder="Select" class="!w-full">
                            <el-option label="Option 1" value="1" />
                            <el-option label="Option 2" value="2" />
                            <el-option label="Option 3" value="3" />
                        </el-select>
                    </el-form-item>
                    <!-- End your form here -->

                <el-button type="primary" @click="submitForm">Submit</el-button>
            </el-form>
        </div>
    </div>
</template>

<script setup>
import { defineProps, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { ElMessage } from 'element-plus';

const props = defineProps({
    form: {
        type: Object,
        // Only for dummy data, you can remove this
        default: () => useForm({
            id: null,
            name: 'Your Name',
            position: '1',
        })
    },
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
