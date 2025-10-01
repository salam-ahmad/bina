<script setup>
import {Link} from '@inertiajs/vue3'

const props = defineProps({orders: Object})
</script>

<template>
    <div class="p-4  container mx-auto">
        <h1 class="text-xl font-bold mb-4">وەرگتنی پارە لە کڕیار</h1>
        <div class="overflow-auto bg-white border rounded dark:bg-transparent">
            <table>
                <thead>
                <tr>
                    <th>پسوڵەی ژمارە</th>
                    <th>کڕیار</th>
                    <th>کۆی گشتی</th>
                    <th>بڕی دراو</th>
                    <th>بڕی ماوە</th>
                    <th>وەرگرتنی پارە</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="o in orders.data" :key="o.id" >
                    <td>#{{ o.id }}</td>
                    <td>{{ o.customer ?? '—' }}</td>
                    <td>{{ Number(o.total).toLocaleString() }}</td>
                    <td>{{ Number(o.paid_amount).toLocaleString() }}</td>
                    <td class=" text-red-500 font-bold">{{ Number(o.due_amount).toLocaleString() }}</td>
                    <td>
                        <Link :href="o.pay_url" class="primary-btn">وەرگرتن</Link>
                    </td>
                </tr>
                <tr v-if="orders.data.length === 0">
                    <td colspan="6" class="p-3 text-center text-gray-500">هیچ نەدۆزرایەوە 🎉</td>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- optional pagination controls -->
    </div>
</template>
