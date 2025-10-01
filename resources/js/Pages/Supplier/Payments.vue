<script setup>
import {Link, router} from '@inertiajs/vue3'
import TextInput from "@/Pages/Components/TextInput.vue";

const props = defineProps({
    supplier: {type: Object, required: true},
    payments: {type: Object, required: true}, // pagination result
    filters: {type: Object, default: () => ({from: null, to: null})},
    total_paid: {type: Number, required: true}
})

const submit = (e) => {
    e.preventDefault()
    const f = new FormData(e.target)
    router.get(route('suppliers.payments', props.supplier.id), {
        from: f.get('from') || null,
        to: f.get('to') || null
    }, {preserveScroll: true})

}

const fmt = (n) => Number(n || 0).toLocaleString()
</script>

<template>
    <div class="p-4 container mx-auto ">
        <h1 class="text-xl font-bold mb-4">پارەی دراو بە — <span class="text-red-500">{{ supplier.name }}</span></h1>
        <form @submit="submit" class="flex gap-4 items-end bg-white border rounded p-4 dark:bg-gray-800">
            <div>
                <label class="dark:text-white text-base">لە ڕێکەوتی</label>
                <input name="from" type="date" :value="filters.from" class="border rounded  px-4 py-2">
            </div>
            <div>
                <label class="dark:text-white text-base ">بۆ ڕێکەوتی</label>
                <input name="to" type="date" :value="filters.to" class="border rounded  px-4 py-2">
            </div>

            <button class="primary-btn py-2">
                <span>گەڕان</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
            </button>
        </form>

        <div class="grid grid-cols-1 my-2">
            <div class="bg-white border rounded py-5 px-4 dark:bg-gray-800 dark:border-blue-200">
                <p class="text-xl text-black dark:text-white">کۆی پارەی دراو</p>
                <p class="text-2xl font-extrabold mt-2 text-red-500">{{ fmt(total_paid) }}</p>
            </div>
        </div>

        <div class="overflow-auto bg-white border rounded dark:bg-transparent dark:border-gray-200">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>ڕێکەوتی پارەدان</th>
                    <th>بڕی پارە</th>
                    <th>تێبینی</th>
                    <th>ژ.پسوڵەی کڕین</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="p in payments.data" :key="p.id">
                    <td>#{{ p.id }}</td>
                    <td>{{ p.paid_at }}</td>
                    <td>{{ fmt(p.amount) }}</td>
                    <td>{{ p.note || '—' }}</td>
                    <td>#{{ p.purchase_id }} ({{ p.purchase_date }})</td>
                    <td>
                        <Link :href="p.purchase_url" class="table_btn">پسوڵەی کڕین</Link>
                    </td>
                </tr>
                <tr v-if="payments.data.length === 0">
                    <td colspan="7" class="p-3 text-center text-gray-500">هیچ نەدۆزرایەوە</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- optional pagination UI -->
    </div>
</template>
