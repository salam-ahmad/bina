<script setup>
import {Link, router} from '@inertiajs/vue3'

const props = defineProps({
    customer: {type: Object, required: true},
    orders: {type: Object, required: true}, // pagination payload
    filters: {type: Object, default: () => ({})}
})

const submit = (e) => {
    e.preventDefault()
    const f = new FormData(e.target)
    router.get(route('customers.orders', props.customer.id), {
        from: f.get('from') || null,
        to: f.get('to') || null,
        status: f.get('status') || null,
        min_due: f.get('min_due') || null,
        max_due: f.get('max_due') || null,
        search_note: f.get('search_note') || null,
    }, {preserveScroll: true})
}

const fmt = (n) => Number(n || 0).toLocaleString()
</script>

<template>
    <div class="p-4 container mx-auto space-y-4">
        <h1 class="text-xl font-bold">فرۆشتن — {{ customer.name }}</h1>

        <form @submit="submit" class="grid grid-cols-2 md:grid-cols-6 gap-2 items-end bg-white p-3 border rounded dark:bg-gray-800 dark:border-white">
            <div>
                <label class="dark:text-white block text-base">لە ڕێکەوتی</label>
                <input type="date" name="from" :value="filters.from" class="border rounded p-2 w-full">
            </div>
            <div>
                <label class="dark:text-white block text-base">بۆ ڕێکەوتی</label>
                <input type="date" name="to" :value="filters.to" class="border rounded p-2 w-full">
            </div>
            <div>
                <label class="dark:text-white block text-base">جۆری پسوڵە</label>
                <select name="status" :value="filters.status || ''" class="border rounded p-2 w-full">
                    <option value="">هەموی</option>
                    <option value="unpaid">قەرز</option>
                    <option value="partial">نیوەقەرز</option>
                    <option value="paid">کاش</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="dark:text-white block text-base">گەڕان لە تێبینی</label>
                <input type="text" name="search_note" :value="filters.search_note" class="border rounded p-2 w-full">
            </div>
            <div class="md:col-span-1">
                <button class="primary-btn">
                    <span>گەڕان</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                </button>
            </div>
        </form>

        <div class="overflow-auto bg-white border rounded dark:bg-transparent dark:border-gray-200">
            <table class="min-w-full">
                <thead>
                <tr>
                    <th>#</th>
                    <th>ڕێکەوت</th>
                    <th>کۆی گشتی</th>
                    <th>پارەی دراو</th>
                    <th>بڕی قەرز</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="o in orders.data" :key="o.id">
                    <td>#{{ o.id }}</td>
                    <td>{{ o.date }}</td>
                    <td>{{ fmt(o.total) }}</td>
                    <td>{{ fmt(o.paid_amount) }}</td>
                    <td :class="o.due_amount > 0 ? 'text-red-600 font-semibold' : ''">
                        {{ fmt(o.due_amount) }}
                    </td>
                    <td class="flex items-center justify-center gap-2 text-center">
                        <Link v-if="o.receive_url" :href="o.receive_url" class="primary-btn">
                            وەرگرتنی پارە
                        </Link>
                        <Link v-if="o.show_url" :href="o.show_url" class="  px-4 py-2 rounded border">
                            بینین
                        </Link>
                    </td>
                </tr>
                <tr v-if="orders.data.length === 0">
                    <td colspan="6" class="p-3 text-center text-gray-500">هیچ نەدۆزرایەوە</td>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- Optional: your pagination component -->
    </div>
</template>
