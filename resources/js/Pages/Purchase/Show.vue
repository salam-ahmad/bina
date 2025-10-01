<script setup>
import {Link} from '@inertiajs/vue3'

const props = defineProps({
    purchase: {type: Object, required: true}
})

const fmt = (n) => Number(n || 0).toLocaleString()
</script>

<template>
    <div class="p-4 container mx-auto space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold">کڕینی ژمارە # {{ purchase.id }}</h1>
            <div class="flex items-center gap-2">
                <Link :href="purchase.pay_url" class="primary-btn">
                    پارەدان
                </Link>
                <Link v-if="purchase.supplier?.purchases_url"
                      :href="purchase.supplier.purchases_url"
                      class="secondary-btn">
                    لیستی پسوڵەکان
                </Link>
            </div>
        </div>

        <!-- Summary card -->
        <div class="grid md:grid-cols-2 gap-4">
            <div class="bg-white border rounded p-4 text-gray-800 dark:text-white dark:bg-gray-800">
                <div class="grid grid-cols-2 gap-2 ">
                    <div>
                        <span>ڕێکەوت:</span> {{ purchase.date }}
                    </div>
                    <div class="col-span-2">
                        <span>تێبینی:</span> {{ purchase.note || '—' }}
                    </div>
                </div>
            </div>

            <div class="bg-white border rounded p-4 text-gray-800 dark:text-white dark:bg-gray-800">
                <div class="grid grid-cols-2 gap-2">
                    <div><span>فرۆشیار:</span> {{ purchase.supplier?.name || '—' }}</div>
                    <div><span>مۆبایل:</span> {{ purchase.supplier?.phone || '—' }}</div>
                    <div class="col-span-2"><span>ناونیشان:</span> {{ purchase.supplier?.address || '—' }}</div>
                </div>
            </div>
        </div>

        <!-- Totals -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-gray-800 dark:text-white ">
            <div class="bg-white border rounded p-4 dark:bg-gray-800">
                <p class="">کۆی گشتی</p>
                <p class="text-2xl font-extrabold mt-1">{{ fmt(purchase.total) }}</p>
            </div>
            <div class="bg-white border rounded p-4 dark:bg-gray-800">
                <p class="">پارەی دراو</p>
                <p class="text-2xl font-extrabold mt-1">{{ fmt(purchase.paid_amount) }}</p>
            </div>
            <div class="bg-white border rounded p-4 dark:bg-gray-800">
                <p class="">بڕی قەرز</p>
                <p class="text-2xl font-extrabold mt-1" :class="purchase.due_amount > 0 ? 'text-red-600' : 'text-green-600'">
                    {{ fmt(purchase.due_amount) }}
                </p>
            </div>
        </div>

        <!-- Items table -->
        <div class="overflow-auto  border rounded dark:border-gray-200">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>ناوی کاڵا</th>
                    <th>بڕ</th>
                    <th>نرخی تاک</th>
                    <th>نرخی کۆ</th>
                    <th>تێبینی</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(it, idx) in purchase.items" :key="it.id">
                    <td>{{ idx + 1 }}</td>
                    <td>{{ it.name || '—' }}</td>
                    <td>{{ fmt(it.quantity) }}</td>
                    <td>{{ fmt(it.price) }}</td>
                    <td>{{ fmt(it.price * it.quantity) }}</td>
                    <td>{{ it.note || '' }}</td>
                </tr>
                <tr v-if="!purchase.items || purchase.items.length === 0">
                    <td colspan="6" class="p-3 text-center text-gray-800">هیچ نەدۆزرایەوە</td>
                </tr>
                </tbody>
                <tfoot v-if="purchase.items && purchase.items.length">
                <tr class="bg-gray-50 dark:text-white dark:bg-gray-800">
                    <td colspan="4" class="px-4 border-t text-right font-semibold ">کۆی گشتی</td>
                     <td colspan="1" class="px-4 text-center font-extrabold">{{ fmt(purchase.total) }}</td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>

        <!-- Optional: payments history -->
        <div v-if="purchase.payments && purchase.payments.length" class="bg-white border rounded p-4">
            <h2 class="font-semibold mb-2">پارەی دراو</h2>
            <ul class="space-y-1 text-sm">
                <li v-for="p in purchase.payments" :key="p.id" class="flex justify-between">
                    <span>{{ p.paid_at }} {{ p.note }}</span>
                    <span class="font-semibold">{{ fmt(p.amount) }}</span>
                </li>
            </ul>
        </div>
    </div>
</template>
