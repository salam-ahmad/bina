<script setup>
import TextInput from "@/Pages/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import Swal from 'sweetalert2'
import {usePermissions} from '@/composables/usePermissions';

const {hasPermission} = usePermissions();

const props = defineProps({
    setting: {
        type: Object,
        required: true
    },
})

const form = useForm({
    name: props.setting.name,
    description: props.setting.description,
    address: props.setting.address,
    phone_1: props.setting.phone_1,
    phone_2: props.setting.phone_2,
    phone_3: props.setting.phone_3,
    phone_4: props.setting.phone_4,
    phone_5: props.setting.phone_5
})

const submit = () => {
    if (hasPermission('settings_edit')) {
        form.put(route('settings.update', {id: props.setting.id}), {
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
}
</script>

<template>
    <Head title="- زیادکردنی ناونیشان"/>
    <div class="container mx-auto p-4">

        <h1 class="text-xl font-semibold  mb-6 text-gray-600">زیادکردنی ناونیشان و ژمارە مۆبایلی دوکان</h1>
        <form @submit.prevent="submit">
            <TextInput name="ناو" v-model="form.name" :message="form.errors.name" type="text"/>
            <TextInput name="وەسفی دوکان" v-model="form.description" :message="form.errors.description" type="text"/>
            <TextInput name="ناونیشانی دوکان" v-model="form.address" type="text"/>
            <TextInput name="ژمارە مۆبایلی 1" v-model="form.phone_1" type="text"/>
            <TextInput name="ژمارە مۆبایلی 2" v-model="form.phone_2" type="text"/>
            <TextInput name="ژمارە مۆبایلی 3" v-model="form.phone_3" type="text"/>
            <TextInput name="ژمارە مۆبایلی 4" v-model="form.phone_4" type="text"/>
            <TextInput name="ژمارە مۆبایلی 5" v-model="form.phone_5" type="text"/>
            <button class="primary-btn " :disabled="form.processing" v-if="hasPermission('settings_edit')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                </svg>
                <span>گۆڕین</span>
            </button>
        </form>
    </div>
</template>

<style scoped>

</style>
