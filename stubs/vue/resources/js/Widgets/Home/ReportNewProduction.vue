<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
        <div class="flex flex-row justify-between mb-4">
            <div>
                <div class="font-bold">Report New Production</div>
                <div class="font-thin text-gray-700 text-sm">Report new production for the selected product</div>
            </div>
        </div>
        <div>
            <el-form ref="formRef" :model="form" label-width="200px" label-position="top"
                require-asterisk-position="right" autocomplete="off">

                    <!-- Your form here -->
                    <el-form-item :error="getFormError('product_id')" prop="product_id" label="Product" :required="true">
                        <el-select v-model="form.product_id" placeholder="Select Product" class="!w-full">
                            <el-option v-for="item in product" :key="item.product_id" :label="item.product" :value="item.product_id" />
                        </el-select>
                    </el-form-item>

                    <div class="grid sm:grid-cols-2 gap-3">
                        <el-form-item :error="getFormError('quantity')" prop="quantity" label="Quantity" :required="true">
                            <el-input type="number" v-model="form.quantity" autocomplete="one-time-code" autocorrect="off" spellcheck="false" class="!w-full" />
                        </el-form-item>
                        <el-form-item :error="getFormError('target')" prop="target" label="Target" :required="true">
                            <el-input type="number" v-model="form.target" autocomplete="one-time-code" autocorrect="off" spellcheck="false" class="!w-full" />
                        </el-form-item>
                    </div>

                    <el-form-item :error="getFormError('media_report')" prop="media_report" label="Report" :required="true">
                        <el-upload
                            drag
                            v-model:file-list="form.media_report"
                            class="!w-full"
                            :auto-upload="false"
                            :limit="1"
                            accept="image/jpeg,image/png,application/pdf"
                        >
                            <el-icon class="el-icon--upload"><upload-filled /></el-icon>
                            <div class="el-upload__text">
                            Drop file here or <em>click to upload</em>
                            </div>
                            <template #tip>
                            <div class="el-upload__tip">
                                jpg/png/pdf files with a size less than 5MB
                            </div>
                            </template>
                        </el-upload>
                    </el-form-item>
                    <!-- End your form here -->

                <el-button type="primary" @click="submitForm">
                    <BsIcon icon="rocket-launch" class="mr-3"></BsIcon> Submit
                </el-button>
            </el-form>
        </div>
    </div>
</template>

<script setup>
import { defineProps, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { ElMessage } from 'element-plus';
import { UploadFilled } from '@element-plus/icons-vue';
import { ElLoading } from 'element-plus';
import BsIcon from '@/Components/BsIcon.vue';

const props = defineProps({
    product: {
        type: Array,
        required: true,
    },
    form: {
        type: Object,
        // Only for dummy data, you can remove this
        default: () => useForm({
            id: null,
            product_id: null,
            quantity: null,
            target: null,
            media_report: [],
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

            // Show fake loading spinner
            const loading = ElLoading.service({
                lock: true,
                customClass: "spinner-loading-img",
                spinner: "disable-default-spinner",
            });
            setTimeout(() => {
                loading.close();
                ElMessage({
                    message: 'Form submitted successfully',
                    type: 'success',
                });
            }, 2000);

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
