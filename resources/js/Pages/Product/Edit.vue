<script setup>
import TextInput from "@/Pages/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import Swal from 'sweetalert2'

const props = defineProps({
    product: {
        type: Object,
        required: true
    }
})
const form = useForm({
    name: props.product.name,
    price: props.product.price,
    rate_in: props.product.rate_in,
    quantity: props.product.quantity,
    code: props.product.code
})

const submit = () => {
    form.put(route('products.update', {id: props.product.id}), {
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
    <Head title="گۆڕینی کاڵا - "/>
    <div class="container mx-auto mt-4">
        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
            <h1 class="text-sm lg:text-xl">گۆڕینی کاڵا</h1>
            <Link :href="route('products.index')" class="primary-btn">
                <span>گەڕانەوە</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18"/>
                </svg>
            </Link>
        </div>
        <form @submit.prevent="submit" class="border border-gray-200 rounded-md p-4 mt-6">

            <TextInput name="ناو" v-model="form.name" :message="form.errors.name" type="text"/>
            <TextInput name="نرخی کڕین" v-model="form.rate_in" :message="form.errors.rate_in" type="text" :thousand-separator="true"/>
            <TextInput name="نرخی فرۆشتن" v-model="form.price" :message="form.errors.price" type="text" :thousand-separator="true"/>
            <TextInput name="بڕی کاڵا" v-model="form.quantity" :message="form.errors.quantity" type="text" :thousand-separator="true"/>
            <TextInput name="کۆد" v-model="form.code" :message="form.errors.code" type="text"/>
            <button type="submit" class="primary-btn" :disabled="form.processing">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"></path>
                </svg>
                <span>گۆڕین</span>
            </button>
        </form>
    </div>
</template>

<style scoped>

</style>
