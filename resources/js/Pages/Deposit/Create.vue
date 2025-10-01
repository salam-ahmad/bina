<script setup>
import TextInput from "@/Pages/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import Swal from 'sweetalert2'


const form = useForm({
    name: null,
    mobile: null,
    address: null,
    note: null,
})

const submit = () => {
    form.post(route('customers.store'), {
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
    <Head title="زیادکردنی کڕیار - "/>
    <div class="container mx-auto mt-4">
        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
            <h1 class="text-sm lg:text-xl">زیادکردنی کڕیار</h1>
            <Link :href="route('customers.index')" class="primary-btn">
                گەڕانەوە
            </Link>
        </div>
        <form @submit.prevent="submit" class="border border-gray-200 rounded-md p-4 mt-6">
            <TextInput name="ناو" v-model="form.name" :message="form.errors.name" type="text"/>
            <TextInput name="مۆبایل" v-model="form.mobile" :message="form.errors.mobile" type="text"/>
            <TextInput name="ناونیشان" v-model="form.address" :message="form.errors.address" type="text"/>
            <div class="mb-4">
                <label for="note">تێبینی :</label>
                <textarea id="note" cols="30" rows="10" v-model="form.note"></textarea>
            </div>
            <button type="submit" class="primary-btn " :disabled="form.processing">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <span>زیادکردن</span>
            </button>
        </form>
    </div>
</template>

<style scoped>

</style>
