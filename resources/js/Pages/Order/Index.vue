<script setup>
import PaginationLinks from "@/Pages/Components/PaginationLinks.vue";

import {router, useForm} from "@inertiajs/vue3";
import {formatNumber, getDate} from "../../utils/numberUtils.js";
import TextInput from "@/Pages/Components/TextInput.vue";
import SearchableSelect from "@/Pages/Components/SearchableSelect.vue";
import {usePermissions} from '@/composables/usePermissions';

const {hasPermission} = usePermissions();

const props = defineProps({
        orders: {
            type: Object,
            required: true
        },
        customers: {
            type: Object,
            required: true
        },
        searchTerm: String,
        filters: {
            type: Object,
            default: () => ({})
        }
    }
)
const form = useForm({
    from_date: props.filters.from_date || null,
    to_date: props.filters.to_date || null,
    customer_id: props.filters.customer_id
})

const submit = () => {
    form.get(route('orders.index'), {
        preserveState: true,
        preserveScroll: true,
        replace: true
    })
}
</script>

<template>
    <Head title="فرۆشراو"/>
    <div class="container mx-auto mt-2 ">
        <div class="flex items-center justify-between  border border-gray-200 rounded-md p-4">
            <h1 class="text-sm lg:text-xl">لیستی فرۆشراو</h1>
            <div class="flex  items-center gap-2 ">
                <!--v-if="hasPermission('order_add')"-->
                <Link class="primary-btn flex items-center justify-between gap-2" :href="route('orders.create')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    زیادکردن
                </Link>
            </div>
        </div>
        <div class="grid items-center sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4 border border-gray-200 rounded-md p-4">
            <TextInput v-model="form.from_date" name="لە ڕێکەوتی" type="date"/>
            <TextInput v-model="form.to_date" name="بۆ ڕێکەوتی" type="date"/>
            <div class="mb-6">
                <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-white">ناوی کڕیار</label>
                <SearchableSelect
                    v-model="form.customer_id"
                    :options="customers"
                    placeholder="ناوی کڕیار"
                    label="name"
                    value-prop="id"
                    no-options-text="هیچ کڕیارێک نەدۆزرایەوە"
                />
            </div>
            <div class="flex items-center gap-2">
                <button class="primary-btn py-2" @click="submit">
                    <span>گەڕان</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="border border-gray-200 rounded-md mt-4 p-4">
            <div class="overflow-x-auto">
                <table class="mt-2 text-center">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>ناوی کڕیار</th>
                        <th>کۆی گشتی</th>
                        <th>ڕێکەوت</th>
                        <th>گۆڕانکاریەکان</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="order in props.orders.data" :key="order.id">
                        <td>{{ order.id }}</td>
                        <td>{{ order.customer.name }}</td>
                        <td>{{ formatNumber(order.total) }}</td>
                        <td>{{ getDate(order.created_at) }}</td>
                        <td class="space-x-1">
                            <Link :href="route('orders.show',{id:order.id})" class="table_btn">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                </svg>
                                <span>زیاتر</span>
                            </Link>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <PaginationLinks :paginator="orders"/>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
