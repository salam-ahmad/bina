<script setup>
import {Link} from '@inertiajs/vue3'

const props = defineProps({purchases: Object})
</script>

<template>
    <div class="container p-4  mx-auto">
        <h1 class="text-xl font-bold mb-4">پارەدان</h1>
        <div class="overflow-auto  border rounded dark:border-gray-200">
            <table class="min-w-full">
                <thead>
                <tr>
                    <th>پسوڵەی کڕین</th>
                    <th>فرۆشیار</th>
                    <th>کۆی گشتی</th>
                    <th>بڕی دراو</th>
                    <th>بڕی ماوە</th>
                    <th>پارەدان</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="p in purchases.data" :key="p.id">
                    <td>#{{ p.id }}</td>
                    <td>{{ p.supplier ?? '—' }}</td>
                    <td>{{ Number(p.total).toLocaleString() }}</td>
                    <td>{{ Number(p.paid_amount).toLocaleString() }}</td>
                    <td class="text-red-600 dark:text-white">{{ Number(p.due_amount).toLocaleString() }}</td>
                    <td>
                        <Link :href="p.pay_url" class="secondary-btn">پارەدان</Link>
                    </td>
                </tr>
                <tr v-if="purchases.data.length === 0">
                    <td colspan="6" class="p-3 text-center text-gray-500">هیچ نەدۆزرایەوە 🎉</td>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- optional pagination controls -->
    </div>
</template>
