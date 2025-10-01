<script setup>
import PaginationLinks from "@/Pages/Components/PaginationLinks.vue";
import {ref, watch} from "vue";
import {debounce} from "lodash";
import {router} from "@inertiajs/vue3";
import {usePermissions} from '@/composables/usePermissions';

const {hasPermission} = usePermissions();

const props = defineProps({
        customers: {
            type: Object,
            required: true
        },
        searchTerm: String,
    }
)
const search = ref(props.searchTerm);

watch(
    search,
    debounce((q) => router.get(route("customers.index"), {search: q}, {preserveState: true}), 500)
);
</script>

<template>
    <Head title="کڕیارەکان - "/>
    <div class="container mx-auto mt-2 ">
        <div class="flex items-center justify-between  border border-gray-200 rounded-md p-4">
            <h1 class="text-sm lg:text-xl">لیستی کڕیارەکان</h1>
            <div class="flex  items-center gap-2 ">
                <input type="search" placeholder="گەڕان..." class="p-2" v-model="search">
                <Link class="primary-btn flex items-center justify-between gap-2" :href="route('customers.create')" v-if="hasPermission('customer_add')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    زیادکردن
                </Link>
            </div>
        </div>
        <div class="border border-gray-200 rounded-md mt-4 p-4">
            <div class="overflow-x-auto">
                <table class="mt-2 text-center">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>ناوی کڕیار</th>
                        <th>ژمارە مۆبایل</th>
                        <th>لیستی پسوڵە</th>
                        <th>پارەی وەرگیراو</th>
                        <th>گۆڕانکاریەکان</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="customer in props.customers.data" :key="customer.id">
                        <td>{{ customer.id }}</td>
                        <td :class="'text-red-500'?customer.debt>0:''">{{ customer.name }}</td>
                        <td>{{ customer.phone }}</td>
                        <td>
                            <Link :href="route('customers.orders', customer.id)" class="table_btn" v-if="hasPermission('customer_bills')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="size-6" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                                </svg>
                                <span>پسوڵەکان</span>
                            </Link>
                        </td>
                        <td>
                            <Link :href="route('customers.payments', customer.id)" class="table_btn">
                                <span>پارەی وەرگیراو</span>
                            </Link>
                        </td>
                        <td>
                            <Link :href="route('customers.show',{id:customer.id})" class="table_btn" v-if="hasPermission('customer_edit')">
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
                <PaginationLinks :paginator="customers"/>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
