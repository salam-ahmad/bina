<script setup>
import TextInput from "@/Pages/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import Swal from 'sweetalert2'
import {usePermissions} from '@/composables/usePermissions';

const {hasPermission} = usePermissions();

const props = defineProps({
        customer: {
            type: Object,
            required: true
        },
    }
)
const form = useForm({
    name: props.customer.name,
    phone: props.customer.phone,
    address: props.customer.address,
    note: props.customer.note
})

const submit = () => {
    // if (hasPermission('customer_edit')) {
        form.put(route('customers.update', {id: props.customer.id}), {
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
    // }

}
</script>

<template>
    <Head title="گۆڕینی کڕیار"/>
    <div class="container mx-auto mt-4">
        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
            <h1 class="text-sm lg:text-xl">گۆڕینی کڕیار / {{ props.customer.name }}</h1>
            <Link :href="route('customers.index')" class="primary-btn">
                <span>گەڕانەوە</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
                </svg>
            </Link>
        </div>
        <form @submit.prevent="submit" class="border border-gray-200 rounded-md p-4 mt-6">
            <TextInput name="ناو" v-model="form.name" :message="form.errors.name" type="text"/>
            <TextInput name="مۆبایل" v-model="form.phone" :message="form.errors.phone" type="text"/>
            <TextInput name="ناونیشان" v-model="form.address" :message="form.errors.address" type="text"/>
            <div class="mb-4">
                <label for="note">تێبینی :</label>
                <textarea id="note" cols="30" rows="10" v-model="form.note"></textarea>
            </div>
            <button type="submit" class="primary-btn " :disabled="form.processing" v-if="hasPermission('customer_edit')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                </svg>
                <span>گۆڕین</span>
            </button>
        </form>
    </div>
</template>
