<script setup>
import {Link, router} from '@inertiajs/vue3'

const props = defineProps({
    rows: {type: Array, required: true},
    filters: {type: Object, default: () => ({from: null, to: null})}
})

const submit = (e) => {
    e.preventDefault()
    const form = new FormData(e.target)
    router.get(route('reports.ar'), {from: form.get('from'), to: form.get('to')}, {preserveScroll: true})
}
</script>

<template>
    <div class="p-4 mx-auto container">
        <h1 class="text-xl font-bold mb-4">قەرزی کڕیارەکان</h1>
        <form @submit="submit" class="flex gap-2 mb-4 items-end">
            <div>
                <label class="block text-sm">لە ڕێکەوتی</label>
                <input name="from" type="date" :value="filters.from" class="border rounded p-2 md:w-48"/>
            </div>
            <div>
                <label class="block text-sm">بۆ ڕێکەوتی</label>
                <input name="to" type="date" :value="filters.to" class="border rounded p-2 md:w-48"/>
            </div>
            <button class="primary-btn py-2">
                <span>گەڕان</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
            </button>
            <Link :href="route('reports.ar_owing')" class="secondary-btn">تەنها قەرزارەکان</Link>
        </form>

        <div class="overflow-auto bg-white border rounded dark:bg-transparent">
            <table class="min-w-full">
                <thead>
                <tr>
                    <th>کڕیار</th>
                    <th>کۆی گشتی</th>
                    <th>بڕی وەرگیراو</th>
                    <th>بڕی ماوە</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="row in rows" :key="row.customer_id">
                    <td>{{ row.customer_name }}</td>
                    <td>{{ Number(row.total_sold).toLocaleString() }}</td>
                    <td>{{ Number(row.total_received).toLocaleString() }}</td>
                    <td class="font-bold"
                        :class="Number(row.balance_due) > 0 ? 'text-red-600' : 'text-green-600'">
                        {{ Number(row.balance_due).toLocaleString() }}
                    </td>
                </tr>
                <tr v-if="rows.length === 0">
                    <td colspan="4" class="p-3 text-center text-gray-500">هیچ نەدۆزرایەوە</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
