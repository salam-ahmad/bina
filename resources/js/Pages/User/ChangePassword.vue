<script setup>
import {useForm} from "@inertiajs/vue3";
import {Head} from '@inertiajs/vue3';
import TextInput from "@/Pages/Components/TextInput.vue";
import Swal from 'sweetalert2'

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.put(route('updatePassword'), {
        onSuccess: () => {
            form.reset(); // Clear the form after success
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: "بەسەرکەوتویی گۆڕا!",
                showConfirmButton: false,
                timerProgressBar: true,
                timer: 2000
            });
        },
        onError: () => {
            // Clear password fields on error
            form.reset('password', 'password_confirmation');
        }
    });
}
</script>

<template>
    <Head title=" - گۆڕینی وشەتێپەڕ"/>
    <div class="container mx-auto mt-4 border border-gray-200 p-4 rounded-md">
        <h1 class="my-4">گۆڕینی وشەتێپەڕ</h1>
        <form @submit.prevent="submit">
            <div class="w-full mx-auto">
                <TextInput
                    name="وشەتێپەڕی کۆن"
                    v-model="form.current_password"
                    :message="form.errors.current_password"
                    type="password"
                    autocomplete="current-password"
                />
                <TextInput
                    name="وشەتێپەڕی نوێ"
                    v-model="form.password"
                    :message="form.errors.password"
                    type="password"
                    autocomplete="new-password"
                />
                <TextInput
                    name="دوپاتی وشەتێپەڕی نوێ"
                    v-model="form.password_confirmation"
                    :message="form.errors.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                />
                <div class="mt-4">
                    <button type="submit" class="primary-btn" :disabled="form.processing">
                        <span v-if="form.processing">چاوەڕێ بە ...</span>
                        <span v-else>گۆڕین</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<style scoped>

</style>
