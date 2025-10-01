<script setup>
import {useForm} from "@inertiajs/vue3";
import TextInput from "@/Pages/Components/TextInput.vue";

const form = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null
})

const submit = () => {
    form.post('/register', {
        onError: () => form.reset(['password', 'password_confirmation'])
    });
}
</script>

<template>
    <Head title="- خۆتۆمارکردن"/>
    <form @submit.prevent="submit">
        <div class="w-2/4 mx-auto">
            <TextInput name="ناو" v-model="form.name" :message="form.errors.name"/>
            <TextInput name="ئیمەیڵ" v-model="form.email" :message="form.errors.email" type="email"/>
            <TextInput name="وشەتێپەڕ" v-model="form.password" :message="form.errors.password" type="password"/>
            <TextInput name="دوپاتی وشەتێپەڕ" v-model="form.password_confirmation" type="password"/>
            <div class="mt-4">
                <button class="primary-btn" :disabled="form.processing">
                    زیادکردن
                </button>
            </div>
        </div>
    </form>
</template>

<style scoped>

</style>
