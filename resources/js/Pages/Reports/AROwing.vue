<script setup>
import {Link, router} from '@inertiajs/vue3'

const props = defineProps({
    rows: {type: Array, required: true},
    filters: {type: Object, default: () => ({from: null, to: null})}
})
const submit = (e) => {
    e.preventDefault()
    const form = new FormData(e.target)
    router.get(route('reports.ar_owing'), {from: form.get('from'), to: form.get('to')}, {preserveScroll: true})
}
</script>

<template>
    <div class="p-4 container  mx-auto">
        <h1 class="text-xl font-bold mb-4">کڕیارە قەرزارەکان</h1>

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
            <Link :href="route('reports.ar')" class="secondary-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="size-5" viewBox="0 0 16 16">
                    <path
                        d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                </svg>
                <span>هەموو کڕیارەکان</span>
            </Link>
        </form>

        <div class="overflow-auto bg-white border rounded dark:bg-transparent">
            <table class="min-w-full  text-base">
                <thead>
                <tr>
                    <th>کڕیار</th>
                    <th>بڕی قەرز</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="row in rows" :key="row.customer_id">
                    <td class="p-2">{{ row.customer_name }}</td>
                    <td class="p-2   text-red-600 font-bold">
                        {{ Number(row.balance_due).toLocaleString() }}
                    </td>
                </tr>
                <tr v-if="rows.length === 0">
                    <td colspan="2" class="p-3 text-center text-gray-500">هیچ کڕیارێکی قەرزار نەدۆزرایەوە 🎉</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
