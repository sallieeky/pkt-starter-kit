<template>

    <Head title="Login" />

    <div class="w-full h-screen bg-cover bg-no-repeat flex flex-col md:flex-row">
        <div class="w-full h-3/4 bg-white order-2 md:w-96 md:h-full md:order-1 relative shadow-xl">
            <div class="bg-[url('/images/pkt-pattern.png')] absolute bottom-0 w-full h-1/2"></div>
            <div class="flex flex-col items-center justify-center h-full relative">
                <img src="/images/logo.png" class="h-32 w-28" />
                <span class="pt-8 font-extrabold text-center text-2xl">Welcome Back !</span>
                <div class="w-80 pt-8">
                    <el-form ref="formLoginRef" :model="form" label-width="200px" label-position="top"
                        require-asterisk-position="right">
                        <el-form-item prop="username" label="Username" :required="true">
                            <el-input size="large" type="text" v-model="form.username" />
                        </el-form-item>
                        <el-form-item prop="password" label="Password" :required="true">
                            <el-input size="large" :type="showPassword ? 'text' : 'password'" v-model="form.password">
                                <template #suffix>
                                    <BsIcon @click="showPassword = !showPassword" icon="eye-slash" class="text-black cursor-pointer" v-if="showPassword" />
                                    <BsIcon @click="showPassword = !showPassword" icon="eye" class="text-black cursor-pointer" v-else />
                                </template>
                            </el-input>
                        </el-form-item>
                        <el-button native-type="submit" type="primary" size="large" class="w-full mt-6" @click="login"
                            :disabled="form.processing" :loading="form.processing">Login</el-button>
                    </el-form>
                </div>
                <p class="absolute bottom-6 text-primary text-center mt-6 text-sm">{{ year }}&copy; PT. Pupuk Kalimantan Timur</p>
            </div>
        </div>

        <div class="order-1 md:order-2 grow relative">
            <div class="mt-6 ml-6">
                <h1 class="text-primary font-extrabold text-2xl md:text-3xl">Welcome To Pupuk Kaltim</h1>
                <p class="text-primary">
                    The innovation Leader of Chemical Industry
                </p>
            </div>
            <div class="absolute bg-[url('/images/bg-login-pabrik.png')] bg-cover bg-no-repeat bg-bottom bottom-0 left-0 right-0 h-full -z-10"></div>
        </div>
    </div>

</template>
<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ElMessage } from 'element-plus';
import BsIcon from '@/Components/BsIcon.vue';

const showPassword = ref(false);
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
