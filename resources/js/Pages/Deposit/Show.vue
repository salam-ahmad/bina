<script setup>
import TextInput from "@/Pages/Components/TextInput.vue";

const props = defineProps({
    customer: {
        type: Object,
        required: true
    }
})
</script>

<template>
    <Head title="بینینی کڕیار - "/>
    <div class="container mx-auto mt-4">
        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
            <h1 class="text-sm lg:text-xl">بینینی کڕیار</h1>
            <Link :href="route('customers.edit',{id:props.customer.id})" class="primary-btn">
                گۆڕین
            </Link>
        </div>
        <div class="border border-gray-200 rounded-md p-4 mt-6">
            <TextInput name="ناو" v-model="props.customer.name" type="text" :readonly="true"/>
            <TextInput name="مۆبایل" v-model="props.customer.mobile" type="text" :readonly="true"/>
            <TextInput name="ناونیشان" v-model="props.customer.address" type="text" :readonly="true"/>
            <div class="mb-4">
                <label for="note">تێبینی :</label>
                <textarea id="note" cols="30" rows="10" v-model="props.customer.note" readonly></textarea>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
