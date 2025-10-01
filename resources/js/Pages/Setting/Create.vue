<script setup>
import TextInput from "@/Pages/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import Swal from 'sweetalert2'

const form = useForm({
    name: null,
    description: null,
    address: null,
    phone_1: null,
    phone_2: null,
    phone_3: null,
    phone_4: null,
    phone_5: null,
})

const submit = () => {
    form.post(route('settings.store'), {
        onSuccess: () => {
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: "بەسەرکەوتویی زیادکرا!",
                showConfirmButton: false,
                timerProgressBar: true,
                timer: 2000
            });
        }
    });
}
</script>

<template>
    <Head title="- زیادکردنی ناونیشان"/>
    <div class="container mx-auto p-4">
        <h1 class="text-xl font-semibold  mb-6 text-gray-600">زیادکردنی ناونیشان و ژمارە مۆبایلی نوسینگە</h1>
        <form @submit.prevent="submit">
            <TextInput name="ناو" v-model="form.name" :message="form.errors.name" type="text"/>
            <TextInput name="وەسفی دوکان" v-model="form.description" :message="form.errors.description" type="text"/>
            <TextInput name="ناونیشان" v-model="form.address" type="text"/>
            <TextInput name="ژمارە مۆبایلی 1" v-model="form.phone_1" type="text"/>
            <TextInput name="ژمارە مۆبایلی 2" v-model="form.phone_2" type="text"/>
            <TextInput name="ژمارە مۆبایلی 3" v-model="form.phone_3" type="text"/>
            <TextInput name="ژمارە مۆبایلی 4" v-model="form.phone_4" type="text"/>
            <TextInput name="ژمارە مۆبایلی 5" v-model="form.phone_5" type="text"/>
            <button class="primary-btn">زیادکردن</button>
        </form>
    </div>
</template>

<style scoped>

</style>
