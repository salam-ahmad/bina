<script setup>
import {Link} from '@inertiajs/vue3'

const props = defineProps({
    order: {type: Object, required: true}
})

const fmt = (n) => Number(n || 0).toLocaleString()
</script>

<template>
    <div class="p-4 container mx-auto space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold">پسوڵەی ژمارە # {{ order.id }}</h1>
            <div class="flex items-center gap-2">
                <Link :href="order.receive_url" class="primary-btn px-4">
                    وەرگرتنی پارە
                </Link>
                <Link v-if="order.customer?.orders_url"
                      :href="order.customer.orders_url"
                      class="px-3 py-2 rounded border">
                    لیستی پسوڵەکان
                </Link>
            </div>
        </div>

        <!-- Summary -->
        <div class="grid md:grid-cols-2 gap-4 ">
            <div class="bg-white dark:bg-gray-800 dark:text-white border rounded p-4">
                <div class="grid grid-cols-2 gap-2 text-base">
                    <div>
                        <span>ڕێکەوت: </span> {{ order.date }}
                    </div>
                    <div class="col-span-2">
                        <span>تێبینی: </span> {{ order.note || '—' }}
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 dark:text-white border rounded p-4">
                <div class="grid grid-cols-2 gap-2 text-base">
                    <div><span>کڕیار :</span> {{ order.customer?.name || '—' }}</div>
                    <div><span>مۆبایل :</span> {{ order.customer?.phone || '—' }}</div>
                    <div class="col-span-2"><span>ناونیشان :</span> {{ order.customer?.address || '—' }}</div>
                </div>
            </div>
        </div>

        <!-- Totals -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-gray-800 dark:text-white ">
            <div class="bg-white border rounded p-4 dark:bg-gray-800 ">
                <p class="text-base">کۆی گشتی</p>
                <p class="text-2xl font-extrabold mt-1">{{ fmt(order.total) }}</p>
            </div>
            <div class="bg-white border rounded p-4 dark:bg-gray-800">
                <p class="text-base">پارەی دراو</p>
                <p class="text-2xl font-extrabold mt-1">{{ fmt(order.paid_amount) }}</p>
            </div>
            <div class="bg-white border rounded p-4 dark:bg-gray-800">
                <p class="text-base">قەرز</p>
                <p class="text-2xl font-extrabold mt-1" :class="order.due_amount > 0 ? 'text-red-600' : 'text-green-600'">
                    {{ fmt(order.due_amount) }}
                </p>
            </div>
        </div>

        <!-- Items -->
        <div class="overflow-auto bg-white border rounded dark:bg-transparent dark:border-gray-200">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>ناوی کاڵا</th>
                    <th>بڕ</th>
                    <th>نرخی تاک</th>
                    <th>کۆی گشتی</th>
                    <th>تێبینی</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(it, idx) in order.items" :key="it.id">
                    <td>{{ idx + 1 }}</td>
                    <td>{{ it.name || '—' }}</td>
                    <td>{{ fmt(it.qty) }}</td>
                    <td>{{ fmt(it.unit_price) }}</td>
                    <td class="font-semibold">{{ fmt(it.line_total) }}</td>
                    <td>{{ it.note || '' }}</td>
                </tr>
                <tr v-if="!order.items || order.items.length === 0">
                    <td colspan="6" class="p-3 text-center text-gray-800">هیچ نەدۆزرایەوە</td>
                </tr>
                </tbody>
                <tfoot v-if="order.items && order.items.length">
                <tr class="bg-gray-50 text-black ">
                    <td colspan="4" class="px-2  text-right font-semibold">کۆی گشتی</td>
                    <td class="p-2  text-center font-extrabold">{{ fmt(order.total) }}</td>
                    <td class="p-2 "></td>
                </tr>
                </tfoot>
            </table>
        </div>

        <!-- Optional: payments history -->
        <div v-if="order.payments && order.payments.length" class="bg-white border rounded p-4 dark:text-white dark:bg-gray-800">
            <h2 class="font-semibold mb-4">پارەی دراو</h2>
            <ul class="space-y-1 text-base">
                <li v-for="p in order.payments" :key="p.id" class="flex justify-between">
                    <span>{{ p.paid_at }}  {{ p.note }}</span>
                    <span class="font-semibold">{{ fmt(p.amount) }}</span>
                </li>
            </ul>
        </div>
    </div>
</template>
