<script setup>
import PaginationLinks from "@/Pages/Components/PaginationLinks.vue";
import {computed, ref, watch, onMounted, nextTick} from "vue";
import SearchableSelect from "@/Pages/Components/SearchableSelect.vue";
import {useForm, Link, router} from "@inertiajs/vue3";
import {formatNumber} from "@/utils/numberUtils.js";
import Swal from "sweetalert2";
import TextInput from "@/Pages/Components/TextInput.vue";
import {debounce} from 'lodash';

const props = defineProps({
    order: Object,
    deposit: Object,
    products: Object,
    customers: Object
})
const search = ref('');
watch(search, debounce((value) => {
    router.get(route('orders.edit', props.order.id), {search: value}, {
        preserveState: true,
        replace: true,
    });
}, 300));
const rows = ref([])
const form = useForm({
    customer_id: props.order.customer_id || '',
    status: props.order.status || 'cash',
    total: props.order.total || 0,
    cashAmount: props.deposit?.amount || 0,
    note: props.order.note || '',
    order_items: [],
})
const deleteRow = (index) => {
    rows.value.splice(index, 1);
}
const addRow = (item) => {
    const existingItemIndex = rows.value.findIndex(row => row.id === item.id);
    if (existingItemIndex !== -1) {
        if (rows.value[existingItemIndex].quantity < item.quantity) {
            rows.value[existingItemIndex].quantity += 1;
        } else {
            showMessage('error', `تەنها ${item.quantity} دانە لەم کاڵایە لە کۆگادا هەیە`)
        }
    } else {
        rows.value.push({...item, quantity: 1, stock: item.quantity});
    }
}
const totalAmount = computed(() => {
    return rows.value.reduce((sum, item) => sum + (item.quantity * item.price), 0);
});
const skipWatch = ref(true);
const handleQuantityInput = (item, index) => {
    if (item.quantity > item.stock) {
        item.quantity = item.stock;
        showMessage('error', `تەنها ${item.stock} دانە لەم کاڵایە لە کۆگادا هەیە`);
    }
    if (item.quantity < 1) {
        item.quantity = 1;
    }
    form.rows = rows.value;
};
onMounted(() => {
    rows.value = props.order.order_items.map(item => ({
        id: item.product.id,
        name: item.product.name,
        code: item.product.code,
        quantity: item.quantity,
        price: item.price,
        stock: item.product.quantity,
        category_id: item.product.category_id,
        rate_in: item.product.rate_in ?? 0
    }));

    // Delay setting skipWatch = false until rows are initialized
    nextTick(() => {
        skipWatch.value = false;
    });
});
watch(() => form.cashAmount, (newVal) => {
    if (newVal > totalAmount.value) {
        form.cashAmount = totalAmount.value;
        showMessage('error', 'ناتوانیت بڕێکی زیاتر لە کۆی گشتی بنووسی');
    }
});
const updateOrder = () => {
    form.total = totalAmount.value;
    form.order_items = rows.value.map(item => ({
        product_id: item.id,
        category_id: item.category_id,
        order_id: props.order.id,
        price: item.price,
        quantity: item.quantity,
        created_at: props.order.created_at,
        rate_in: item.rate_in ?? 0,
    }));

    const formData = {
        ...form.data(),
        order_items: JSON.stringify(form.order_items),
        total: Number(totalAmount.value),
    };
    console.log(formData)
    if (form.status === 'loan' && form.customer_id === '') {
        showMessage('error', 'مادام قەرزە ناوێک هەڵبژێرە');
        return;
    }

    router.put(route('orders.update', props.order.id), formData, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showMessage('success', 'بەسەرکەوتویی نوێکرایەوە');
        },
        onError: () => {
            showMessage('error', 'هەڵەیەک ڕویدا لە کاتی نوێکردنەوە.');
        }
    });
}
const showMessage = (icon, title) => {
    Swal.fire({
        position: "top-end",
        icon: icon,
        title: title,
        showConfirmButton: false,
        timerProgressBar: true,
        timer: 2000
    });
}
</script>
<template>
    <div class="container mx-auto mt-4">
        <!-- Search & Back -->
        <div class="flex gap-4 items-center justify-between p-4 border border-gray-200 rounded-md mb-4">
            <input type="search" class="flex-1" placeholder="گەڕان بەپێی ناو یان کۆد ..." v-model="search">
            <Link :href="route('orders.index')" class="primary-btn">گەڕانەوە</Link>
        </div>
        <!-- Product Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4 mt-4 p-4 border border-gray-200 rounded-md">
            <div class="flex flex-col h-full p-4 border border-gray-400 rounded-md text-center" v-for="item in props.products.data" :key="item.id">
                <div class="space-y-2 flex-grow">
                    <h5 class="text-xl"> نرخ/ <span class="text-red-500">{{ formatNumber(item.price) }}</span></h5>
                    <p>{{ item.name }}</p>
                    <p>کۆد / {{ item.code }}</p>
                    <p>بڕی ماوە / <span class="text-red-500">{{ item.quantity }}</span></p>
                </div>
                <button class="primary-btn flex items-center justify-center gap-2 mt-auto " @click="addRow(item)" :disabled="item.quantity<1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-plus" viewBox="0 0 16 16">
                        <path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9z"/>
                        <path
                            d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                    </svg>
                    <span>زیادکردن</span>
                </button>
            </div>
        </div>
        <!-- Pagination -->
        <div class="mt-6">
            <PaginationLinks :paginator="props.products"/>
        </div>
        <hr class="my-5">
        <!-- Order Form -->
        <div v-if="rows.length > 0">
            <!-- Form Inputs -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-2 my-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">ناوی کڕیار</label>
                    <SearchableSelect
                        v-model="form.customer_id"
                        :options="props.customers ?? []"
                        placeholder="ناوی کڕیار"
                        label="name"
                        value-prop="id"
                        no-options-text="هیچ کڕیارێک نەدۆزرایەوە"
                    />
                </div>
                <TextInput
                    type="number"
                    :thousand-separator="true"
                    v-model="form.cashAmount"
                    :max="totalAmount"
                    name="بڕی پارەی وەرگیراو"
                    min="0"
                    :disabled="totalAmount === 0"
                    placeholder="بڕی پارەی وەرگیراو بنوسە"
                />
                <div>
                    <label class="block text-sm font-medium text-gray-700">جۆری فرۆشتن</label>
                    <select v-model="form.status" class="w-full px-3 py-2 border border-blue-500 rounded-md">
                        <option value="" disabled>جۆری مامەڵە</option>
                        <option value="cash">نەقد</option>
                        <option value="loan">قەرز</option>
                    </select>
                </div>
                <TextInput v-model="form.note" name="تێبینی" class="xl:col-span-2"/>
                <div class="flex items-center justify-around">
                    <h2 class="text-end text-red-500 text-2xl">{{ formatNumber(totalAmount) }}</h2>
                    <button class="text-white rounded-md bg-green-600 hover:bg-green-700 px-3 py-2 cursor-pointer" @click="updateOrder">نوێکردنەوە</button>
                </div>
            </div>
            <!-- Order Items Table -->
            <table class="w-full border-collapse border border-gray-300 text-center">
                <thead>
                <tr class="bg-gray-300">
                    <th>کۆد</th>
                    <th>ناوی کاڵا</th>
                    <th>نرخ</th>
                    <th>بڕ</th>
                    <th>کۆ</th>
                    <th>سڕینەوە</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, index) in rows" :key="index">
                    <td>{{ item.code }}</td>
                    <td>{{ item.name }}</td>
                    <td><input type="number" min="250" step="250" class="text-center" v-model.number="item.price"/></td>
                    <td><input type="number" min="1" :max="item.stock" class="text-center" v-model.number="item.quantity" @input="handleQuantityInput(item, index)" :disabled="item.stock<1"/></td>
                    <td>{{ formatNumber(item.quantity * item.price) }}</td>
                    <td>
                        <button class="bg-red-500 text-white px-2 py-1 rounded" @change="deleteRow(index)">سڕینەوە</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <h3 class="text-center text-2xl">کاڵایەک هەڵبژێرە بۆ نوێکردنەوە.</h3>
        </div>
    </div>
</template>
