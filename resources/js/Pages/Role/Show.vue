<script setup>
import TextInput from "@/Pages/Components/TextInput.vue";

const props = defineProps({
        role: {
            type: Object,
            required: true
        },
    }
)

</script>

<template>
    <Head title="بینینی ڕۆڵ - "/>
    <div class="container mx-auto mt-4">
        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
            <h1 class="text-sm lg:text-xl">بینینی ڕۆڵ / <span class="text-red-600">{{ props.role.name }}</span></h1>
            <Link :href="route('roles.edit',{id:props.role.id})" class="primary-btn">
                <span>گۆڕین</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="size-5" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                </svg>
            </Link>
        </div>
        <div class="border border-gray-200 rounded-md p-4 mt-6">
            <TextInput name="ناوی کوردی" v-model="props.role.label" type="text" :readonly="true"/>
            <TextInput name="ناوی ئینگلیزی" v-model="props.role.name" type="text" :readonly="true"/>
        </div>
    </div>
</template>

<style scoped>

</style>
