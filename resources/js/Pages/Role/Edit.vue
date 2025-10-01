<script setup>
import TextInput from "@/Pages/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import Swal from 'sweetalert2'

const props = defineProps({
    role: Object
})
const form = useForm({
    name: props.role.name,
    label: props.role.label
})

const submit = () => {
    form.put(route('roles.update', props.role.id), {
        onSuccess: () => {
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: "بەسەرکەوتویی گۆڕا!",
                showConfirmButton: false,
                timerProgressBar: true,
                timer: 2000
            });
        }
    });
}
</script>

<template>
    <Head title="- گۆڕینی ڕۆڵ"/>
    <div class="container mx-auto mt-4">
        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
            <h1 class="text-xl font-semibold  mb-6 text-gray-600">گۆڕینی ڕۆڵ / <span class="text-red-500">{{ props.role.name }}</span></h1>
            <Link :href="route('roles.index')" class="primary-btn">
                <span>گەڕانەوە</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
                </svg>

            </Link>
        </div>

        <div class="w-full border border-gray-200 rounded-md p-4 mt-6">
            <form @submit.prevent="submit">
                <TextInput name="ناوی کوردی" v-model="form.label" :message="form.errors.label" type="text"/>
                <TextInput name="ناوی ئینگلیزی" v-model="form.name" :message="form.errors.name" type="text"/>
                <button class="primary-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"></path>
                    </svg>
                    <span>گۆڕین</span>
                </button>
            </form>
        </div>
    </div>

</template>

<style scoped>
</style>
