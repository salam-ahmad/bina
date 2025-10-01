<script setup>
import {reactive} from "vue";
import {useForm} from "@inertiajs/vue3";
import TextInput from "@/Pages/Components/TextInput.vue";
import Guest from "@/Layouts/Guest.vue";

const form = useForm({
    email: null,
    password: null,
    remember: null
})

const submit = () => {
    form.post('/login', {
        onError: () => form.reset(['password'])
    });
}
defineOptions({layout: Guest})
</script>

<template>
    <Head title="- چونەژورەوە"/>
    <div class="container mx-auto p-4 dark:bg-gray-800 rounded-md bg-white dark:text-white">
        <div class="space-y-6 text-center pb-4 m-4  py-4">
            <h1 class="text-3xl font-semibold ">بەخێربێیتەوە!</h1>
            <p class="text-2xl font-semibold ">بۆ بەردەوامبوون ، تکایە داخڵی هەژمارەکەت بە.</p>
        </div>
        <form @submit.prevent="submit">
            <TextInput name="ئیمەیڵ" v-model="form.email" :message="form.errors.email" type="email"/>
            <TextInput name="وشەتێپەڕ" v-model="form.password" :message="form.errors.password" type="password"/>
            <div class="flex items-center justify-start gap-4">
                <label for="remember" class="dark:text-white">پاشەکەوتم بکە :</label>
                <input type="checkbox" id="remember" v-model="form.remember">
            </div>
            <div class="mt-4">
                <button class="primary-btn" :disabled="form.processing">
                    چونەژورەوە
                </button>
            </div>
        </form>
    </div>

</template>

<style scoped>

</style>
