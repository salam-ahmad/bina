<script setup>
import {useForm} from "@inertiajs/vue3";
import TextInput from "@/Pages/Components/TextInput.vue";
import SearchableSelect from "@/Pages/Components/SearchableSelect.vue";

const props = defineProps({
    roles: {
        type: Object,
        required: true
    }
})

const form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
    role_id: null,
})

const submit = () => {
    form.post('/register', {
        onError: () => form.reset(['password', 'password_confirmation'])
    });
}
</script>

<template>
    <Head title="- خۆتۆمارکردن"/>
    <div class="container mx-auto mt-4">
        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
            <h1 class="text-xl font-semibold  mb-6 text-gray-600">زیادکردنی بەکارهێنەر</h1>
            <Link :href="route('users.index')" class="primary-btn">
                <span>گەڕانەوە</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18"/>
                </svg>

            </Link>
        </div>
        <form @submit.prevent="submit" class="mt-4 border rounded-md p-4 border-gray-200">
            <div class="w-full mx-auto">
                <TextInput name="ناو" v-model="form.name" :message="form.errors.name"/>
                <TextInput name="ئیمەیڵ" v-model="form.email" :message="form.errors.email" type="email"/>
                <TextInput name="وشەتێپەڕ" v-model="form.password" :message="form.errors.password" type="password"/>
                <TextInput name="دوپاتی وشەتێپەڕ" v-model="form.password_confirmation" type="password"/>
                <div class="mb-6">
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">ناوی ڕۆڵ</label>
                    <SearchableSelect
                        v-model="form.role_id"
                        :options="roles"
                        placeholder="ناوی ڕۆڵ"
                        label="name"
                        value-prop="id"
                        :error="form.errors.role_id"
                        no-options-text="هیچ  نەدۆزرایەوە"
                    />
                </div>
                <div class="mt-4">
                    <button class="primary-btn" :disabled="form.processing">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="size-5" viewBox="0 0 16 16">
                            <path d="M11 2H9v3h2z"/>
                            <path
                                d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z"/>
                        </svg>
                        <span>زیادکردن</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<style scoped>

</style>
