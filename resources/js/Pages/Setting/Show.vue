<script setup>
import TextInput from "@/Pages/Components/TextInput.vue";
import {Link} from "@inertiajs/vue3";
import {usePermissions} from '@/composables/usePermissions';

const {hasPermission} = usePermissions();

const props = defineProps({
    setting: {
        type: Object,
        required: true
    },
})

</script>

<template>
    <Head title=" ناونیشان"/>
    <div class="container mx-auto mt-2">
        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-md">
            <h1 class="text-xl font-semibold text-gray-600"> ناونیشان و ژمارە مۆبایلی دوکان</h1>
            <!--v-if="hasPermission('settings_edit')"-->
            <Link :href="route('settings.edit',{id:props.setting.id})" class="primary-btn">
                <span>گۆڕین</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="size-5" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                </svg>
            </Link>
        </div>
        <div class="w-full border border-gray-200 rounded-md p-4 mt-2">
            <form @submit.prevent="submit">
                <TextInput :readonly="true" name="ناو" v-model="props.setting.name" type="text"/>
                <TextInput :readonly="true" name="وەسفی دوکان" v-model="props.setting.description" type="text"/>
                <TextInput :readonly="true" name="ناونیشانی دوکان" v-model="props.setting.address" type="text"/>
                <TextInput :readonly="true" name="ژمارە مۆبایلی 1" v-model="props.setting.phone_1" type="text"/>
                <TextInput :readonly="true" name="ژمارە مۆبایلی 2" v-model="props.setting.phone_2" type="text"/>
                <TextInput :readonly="true" name="ژمارە مۆبایلی 3" v-model="props.setting.phone_3" type="text"/>
                <TextInput :readonly="true" name="ژمارە مۆبایلی 4" v-model="props.setting.phone_4" type="text"/>
                <TextInput :readonly="true" name="ژمارە مۆبایلی 5" v-model="props.setting.phone_5" type="text"/>
            </form>
        </div>
    </div>
</template>

<style scoped>

</style>
