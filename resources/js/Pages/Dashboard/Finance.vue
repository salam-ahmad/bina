<script setup>
import {Link, router} from '@inertiajs/vue3'
import {usePermissions} from '@/composables/usePermissions';

const {hasPermission} = usePermissions();

const props = defineProps({
    metrics: {
        type: Object,
        required: true,
    }
})

// filter submit
const submit = (e) => {
    e.preventDefault()
    const form = new FormData(e.target)
    router.get(route('dashboard.finance'), {
        from: form.get('from') || null,
        to: form.get('to') || null
    }, {preserveScroll: true, replace: true})
}

const fmt = (n) => Number(n || 0).toLocaleString()
</script>

<template>
    <div class=" container mx-auto mt-4 space-y-4">
        <div class="mb-12">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-100">داشبۆرد</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <Link :href="route('reports.ar_owing')" class="primary-btn h-12" v-if="hasPermission('show_debtors')">قەرزدارەکان</Link>
                <Link :href="route('payments.receive_form')" class="primary-btn h-12" v-if="hasPermission('receive_money')">وەرگرتنی پارە</Link>
                <Link :href="route('reports.ap')" class="primary-btn h-12" v-if="hasPermission('suppliers_debt')">قەرزی فرۆشیاران</Link>
                <Link :href="route('payments.pay_form')" class="primary-btn h-12" v-if="hasPermission('pay_customer')">پارەدان بە فرۆشیار</Link>
            </div>
        </div>

        <form @submit="submit" class="flex flex-wrap gap-3 items-end border border-gray-200 rounded-md p-3">
            <div>
                <label class="block text-sm dark:text-gray-200">لە ڕێکەوتی</label>
                <input type="date" name="from" :value="metrics.from" class="border rounded p-2 md:w-48">
            </div>
            <div>
                <label class="block text-sm dark:text-gray-200">بۆ ڕێکەوتی</label>
                <input type="date" name="to" :value="metrics.to" class="border rounded p-2 md:w-48">
            </div>
            <button class="primary-btn">
                <span>گەڕان</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
            </button>
        </form>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 border border-gray-200 rounded-md p-3">
            <div class="text-center rounded-lg border bg-white p-10 dark:bg-gray-800">
                <p class="text-xl dark:text-white">کۆی فرۆشراو</p>
                <p class="text-3xl font-extrabold mt-1 dark:text-white">{{ fmt(metrics.total_sold) }}</p>
            </div>
            <div class="text-center rounded-lg border bg-white p-10 dark:bg-gray-800">
                <p class="text-xl dark:text-white">کۆی کڕدراو</p>
                <p class="text-3xl font-extrabold mt-1 dark:text-white">{{ fmt(metrics.total_bought) }}</p>
            </div>
            <div class="text-center rounded-lg border bg-white p-10 dark:bg-gray-800">
                <p class="text-xl dark:text-white">کۆی قەرزی کڕیارەکان</p>
                <p class="text-3xl font-extrabold mt-1 dark:text-white text-red-600">{{ fmt(metrics.ar_due) }}</p>
            </div>
            <div class="text-center rounded-lg border bg-white p-10 dark:bg-gray-800">
                <p class="text-xl dark:text-white">کۆی قەرزەکانم</p>
                <p class="text-3xl font-extrabold mt-1 dark:text-white text-red-600">{{ fmt(metrics.ap_due) }}</p>
            </div>
            <div class="text-center rounded-lg border bg-white p-10 dark:bg-gray-800">
                <p class="text-xl dark:text-white">کۆی پارەی وەرگیراو</p>
                <p class="text-3xl font-extrabold mt-1 dark:text-white">{{ fmt(metrics.cash_in) }}</p>
            </div>
            <div class="text-center rounded-lg border bg-white p-10 dark:bg-gray-800">
                <p class="text-xl dark:text-white">کۆی پارەی دراو</p>
                <p class="text-3xl font-extrabold mt-1 dark:text-white">{{ fmt(metrics.cash_out) }}</p>
            </div>
            <div class="text-center rounded-lg border bg-white p-10 dark:bg-gray-800">
                <p class="text-xl dark:text-white">نرخی کاڵاکانی ناو مەخزەن</p>
                <p class="text-3xl font-extrabold mt-1 dark:text-white">{{ fmt(metrics.inventory_value) }}</p>
            </div>
        </div>

    </div>
</template>
