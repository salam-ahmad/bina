<script setup>
import PaginationLinks from "@/Pages/Components/PaginationLinks.vue";
import {ref, watch} from "vue";
import {debounce} from "lodash";
import {router} from "@inertiajs/vue3";
import {formatNumber} from "../../utils/numberUtils.js";

const props = defineProps({
        products: {
            type: Object,
            required: true
        },
        searchTerm: String,
    }
)
const search = ref(props.searchTerm);

watch(
    search,
    debounce((q) => router.get(route("products.index"), {search: q}, {preserveState: true}), 500)
);
</script>

<template>
    <Head title="کاڵاکان - "/>
    <div class="container mx-auto mt-2 ">
        <div class="flex items-center justify-between  border border-gray-200 rounded-md p-4">
            <h1 class="text-sm lg:text-xl">لیستی کاڵاکان</h1>
            <div class="flex  items-center gap-2 ">
                <input type="search" placeholder="گەڕان..." class="p-2" v-model="search">
                <Link class="primary-btn flex items-center justify-between gap-2" :href="route('products.create')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    زیادکردن
                </Link>
            </div>
        </div>
        <div class="border border-gray-200 rounded-md mt-4 p-4">
            <div class="overflow-x-auto">
                <table class="mt-2">
                    <thead>
                    <tr>
                        <th>ID</th>
                         <th>ناوی کاڵا</th>
                        <th>کۆدی کاڵا</th>
                        <th>نرخی کڕین</th>
                        <th>نرخی فرۆشتن</th>
                        <th>بڕی کاڵا</th>
                        <th>گۆڕانکاریەکان</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="product in props.products.data" :key="product.id">
                        <td>{{ product.id }}</td>
                         <td :class="{'text-red-500': product.quantity < 1}">{{ product.name }}</td>
                        <td>{{ product.code }}</td>
                        <td>{{ formatNumber(product.rate_in) }}</td>
                        <td>{{ formatNumber(product.price) }}</td>
                        <td>{{ formatNumber(product.quantity) }}</td>
                        <td class="space-x-1">
                            <Link :href="route('products.show',{id:product.id})" class="table_btn">
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
                <PaginationLinks :paginator="products"/>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
