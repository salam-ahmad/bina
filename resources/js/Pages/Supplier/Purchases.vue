<script setup>
import {Link, router} from '@inertiajs/vue3'

const props = defineProps({
    supplier: {type: Object, required: true},
    purchases: {type: Object, required: true}, // pagination object
    filters: {type: Object, default: () => ({})}
})

const submit = (e) => {
    e.preventDefault()
    const f = new FormData(e.target)
    router.get(route('suppliers.purchases', props.supplier.id), {
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
        <h1 class="text-xl font-bold">
            کڕین —
            <span class="text-red-500">{{ supplier.name }}</span>
        </h1>

        <p class="p-4 bg-green-200" v-if="$page.props.flash.message">{{ $page.props.flash.message }}</p>
        <form @submit="submit" class="grid grid-cols-2 md:grid-cols-6 gap-2 items-end bg-white p-3 border rounded dark:bg-gray-800 dark:border-gray-200 ">
            <div>
                <label class="block text-base dark:text-white">لە ڕێکەوتی</label>
                <input type="date" name="from" :value="filters.from" class="border rounded p-2 w-full">
            </div>
            <div>
                <label class="block text-base dark:text-white">بۆ ڕێکەوتی</label>
                <input type="date" name="to" :value="filters.to" class="border rounded p-2 w-full">
            </div>
            <div>
                <label class="block text-base dark:text-white">جۆری پسوڵە</label>
                <select name="status" :value="filters.status || ''" class="border rounded p-2 w-full">
                    <option value="">هەموی</option>
                    <option value="unpaid">قەرز</option>
                    <option value="partial">نیوەقەرز</option>
                    <option value="paid">کاش</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-base dark:text-white">گەڕان بەپێی تێبینی</label>
                <input type="text" name="search_note" :value="filters.search_note" class="border rounded p-2 w-full">
            </div>
            <div class="md:col-span-1">
                <button class="primary-btn px-4">گەڕان</button>
            </div>
        </form>

        <div class="overflow-auto bg-white border rounded dark:bg-transparent dark:border-gray-200">
            <table class="min-w-full text-base">
                <thead>
                <tr>
                    <th>#</th>
                    <th>ڕێکەوت</th>
                    <th>کۆی گشتی</th>
                    <th>بڕی دراو</th>
                    <th>بڕی قەرز</th>
                    <th ></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="p in purchases.data" :key="p.id">
                    <td>#{{ p.id }}</td>
                    <td>{{ p.date }}</td>
                    <td>{{ fmt(p.total) }}</td>
                    <td>{{ fmt(p.paid_amount) }}</td>
                    <td :class="p.due_amount > 0 ? 'text-red-500 ' : ''">
                        {{ fmt(p.due_amount) }}
                    </td>
                    <td class="flex gap-2" >
                        <Link v-if="p.pay_url" :href="p.pay_url" class="table_btn">
                            پارەدان
                        </Link>
                        <Link v-if="p.show_url" :href="p.show_url" class="secondary-btn">
                            بینین
                        </Link>
                    </td>
                </tr>
                <tr v-if="purchases.data.length === 0">
                    <td colspan="7" class="p-3 text-center text-gray-500">هیچ نەدۆزرایەوە</td>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- Optional: your pagination component -->
    </div>
</template>
