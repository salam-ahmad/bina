<script setup>
import {useForm} from "@inertiajs/vue3";
import TextInput from "@/Pages/Components/TextInput.vue";
import Swal from 'sweetalert2'
import SearchableSelect from "@/Pages/Components/SearchableSelect.vue";

const props = defineProps({
    user: Object,
    roles: Array
})

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role_id: props.user.role_id
})

const submit = () => {
    form.put(`/users/${props.user.id}`, {
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
    <Head title="- گۆڕانکاری بەکارهێنەر"/>
    <div class="container mx-auto mt-4">
        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
            <h1 class="text-sm lg:text-xl">گۆڕینی بەکارهێنەر /
                <span class="text-red-500">{{ props.user.name }}</span>
            </h1>
            <Link :href="route('users.index')" class="primary-btn">
                <span>گەڕانەوە</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18"/>
                </svg>

            </Link>
        </div>
        <div class="w-full border border-gray-200 rounded-md p-4 mt-6">
            <form @submit.prevent="submit">
                <div class="w-full mx-auto">
                    <TextInput name="ناو" v-model="form.name" :message="form.errors.name"/>
                    <TextInput name="ئیمەیڵ" v-model="form.email" :message="form.errors.email" type="email"/>
                    <div class="mb-6">
                        <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">ناوی ڕۆڵ</label>
                        <SearchableSelect
                            v-model="form.role_id"
                            :options="roles"
                            placeholder="ناوی ڕۆڵ"
                            label="name"
                            value-prop="id"
                            :error="form.errors.role_id"
                            no-options-text="هیچ ڕۆڵێک نەدۆزرایەوە"
                        />
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="primary-btn" :disabled="form.processing">
                            گۆڕین
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
</style>
