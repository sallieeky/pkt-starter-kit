<template>

    <Head title="Login" />
    <div class="bg-[url('/images/background-pkt.jpg')] w-full h-screen bg-cover bg-no-repeat flex">
        <div class="absolute">
            <img src="/images/logo-white.png" class="w-64 ml-24 mt-6" />
        </div>
        <div class="grow my-auto mx-24">
            <h1 class="text-4xl font-bold text-white">Pupuk Kaltim Smart Production</h1>
            <p class="text-white pt-6">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam varius, arcu in sagittis congue, ante
                turpis porta purus, nec dignissim mauris elit vel odio. Fusce molestie felis sed facilisis egestas.
                Nullam pretium luctus urna nec egestas. Vestibulum tempus nunc tortor, ac feugiat lorem facilisis ut.
                Donec mattis ex ut venenatis sagittis. Class aptent taciti sociosqu ad litora torquent per conubia
                nostra, per inceptos himenaeos. Integer vitae quam lorem.
            </p>
        </div>
        <div class="rounded-2xl bg-white p-8 shadow-xl my-auto grow-0 mr-10">
            <div class="flex align-middle items-center flex-col">
                <img src="/images/logo.png" class="h-32 w-28" />
                <span class="pt-8 font-extrabold text-center text-2xl">Welcome Back !</span>
                <div class="w-80 pt-8">
                    <el-form ref="formLoginRef" :model="form" label-width="200px" label-position="top"
                        require-asterisk-position="right">
                        <el-form-item prop="username" label="Username" :required="true">
                            <el-input size="large" type="text" v-model="form.username" />
                        </el-form-item>
                        <el-form-item prop="password" label="Password" :required="true">
                            <el-input size="large" type="password" v-model="form.password" />
                        </el-form-item>
                        <el-button native-type="submit" type="primary" size="large" class="w-full mt-6" @click="login"
                            :disabled="form.processing" :loading="form.processing">Login</el-button>
                    </el-form>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0">
            <p class="text-white ml-24 mb-6">{{ year }}&copy; PT. Pupuk Kalimantan Timur</p>
        </div>
    </div>
</template>
<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ElMessage } from 'element-plus';

const year = new Date().getFullYear();

const form = useForm({
    username: '',
    password: '',
});

const formLoginRef = ref();

const login = async () => {
    await formLoginRef.value.validate(async (valid, fields) => {
        if (valid) {
            form.post('/login', {
                onSuccess: () => {
                    ElMessage({
                        message: 'Login Success !!!',
                        type: 'success',
                    });
                },
                onError: (errors) => {
                    ElMessage({
                        message: errors.message,
                        type: 'error',
                    });
                }
            });
        }
    })
}


</script>