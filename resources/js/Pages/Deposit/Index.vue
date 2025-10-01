<script setup>
import PaginationLinks from "@/Pages/Components/PaginationLinks.vue";
import {ref, watch} from "vue";
import {debounce} from "lodash";
import {router} from "@inertiajs/vue3";

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
    <div class="container mx-auto mt-4 ">
        <div class="flex items-center justify-between  border border-gray-200 rounded-md p-4">
            <h1 class="text-sm lg:text-xl">لیستی کڕیارەکان</h1>
            <div class="flex  items-center gap-2 ">
                <input type="search" placeholder="گەڕان..." class="p-2" v-model="search">
                <Link class="primary-btn flex items-center justify-between gap-2" :href="route('customers.create')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    زیادکردن
                </Link>
            </div>
        </div>
        <div class="border border-gray-200 rounded-md mt-4 p-4">
            <div class="overflow-x-auto">
                <table class="mt-8 text-center">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>ناوی کڕیار</th>
                        <th>ژمارە مۆبایل</th>
                        <th>بڕی قەرز</th>
                        <th>تێبینی</th>
                        <th>گۆڕانکاریەکان</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="customer in props.customers.data" :key="customer.id">
                        <td>{{ customer.id }}</td>
                        <td :class="'text-red-500'?customer.debt_amount>0:''">{{ customer.name }}</td>
                        <td>{{ customer.mobile }}</td>
                        <td>{{ customer.debt_amount }}</td>
                        <td>{{ customer.note }}</td>
                        <td class="space-x-1">
                            <Link :href="route('customers.show',{id:customer.id})" class="bg-blue-600 text-white rounded-md px-3 py-1">زیاتر</Link>
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
