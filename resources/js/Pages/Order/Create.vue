<script setup>
import PaginationLinks from "@/Pages/Components/PaginationLinks.vue";
import {computed, ref, watch} from "vue";
import SearchableSelect from "@/Pages/Components/SearchableSelect.vue";
import {useForm, Link} from "@inertiajs/vue3";
import {formatNumber} from "@/utils/numberUtils.js";
import Swal from "sweetalert2";
import TextInput from "@/Pages/Components/TextInput.vue";
import {router} from '@inertiajs/vue3';
import {debounce} from 'lodash';

const props = defineProps({
    customers: {
        type: Object,
        required: true
    },
    products: {
        type: Object,
        required: true
    },
    filters: Object,
})
const search = ref(props.filters?.search || '');
watch(search, debounce((value) => {
    router.get(route('orders.create'), {search: value}, {
        preserveState: true,
        replace: true,
    });
}, 300));
const form = useForm({
    customer_id: '',
    status: 'cash',
    total: 0,
    paid_amount: 0,
    note: '',
    order_items: [],
});
const rows = ref([])

const deleteRow = (index) => { // Fixed parameter name
    rows.value.splice(index, 1);
}

const addRow = (item) => {
    // Check if item already exists in rows
    const existingItemIndex = rows.value.findIndex(row => row.id === item.id);

    if (existingItemIndex !== -1) {
        // If item exists, increment quantity only if it doesn't exceed available stock
        if (rows.value[existingItemIndex].quantity < item.quantity) {
            rows.value[existingItemIndex].quantity += 1;
        } else {
            // Show warning if trying to add more than available stock
            showMessage('error', `تەنها ${item.quantity} دانە لەم کاڵایە لە کۆگادا هەیە`)
        }
    } else {
        // If item doesn't exist, add new row
        rows.value.push({...item, quantity: 1, stock: item.quantity});
    }
    watch(rows, (newRows) => {
        form.rows = newRows;
    }, {deep: true});

    const totalAmount = computed(() => {
        return rows.value.reduce((sum, item) => {
            return sum + (item.quantity * item.price);
        }, 0);
    });

    const saveOrder = async () => {
        try {
            // Update form with current rows and total
            form.rows = rows.value;
            form.total = totalAmount.value;
            // Submit the form
            form.post(route('orders.store'), {
                onSuccess: () => {
                    showMessage('success', 'بەسەرکەوتویی زیادکرا')
                    // Reset form and rows
                    form.reset();
                    rows.value = [];
                },
                onError: (errors) => {
                    console.error('Form errors:', errors);
                    showMessage('error', 'هەڵەیەک ڕوویدا');
                }
            });
        } catch (error) {
            console.error('Error saving order:', error);
            showMessage('error', 'ببورە هەڵە ڕویدا');
        }
    }
}
const totalAmount = computed(() => {
    return rows.value.reduce((sum, item) => {
        return sum + (item.quantity * item.price);
    }, 0);
});
// Watch for changes in rows to update form
watch(rows, (newRows) => {
    form.rows = newRows;
}, {deep: true});
watch(rows, (newRows) => {
    newRows.forEach((row) => {
        if (row.quantity > row.stock) {
            row.quantity = row.stock;
            showMessage('error', `تەنها ${row.stock} دانە لەم کاڵایە لە کۆگادا هەیە`);
        }
        if (row.quantity < 1) {
            row.quantity = 1;
        }
    });
    form.rows = newRows;
}, {deep: true});
watch(() => form.paid_amount, (newVal) => {
    if (newVal > totalAmount.value) {
        form.paid_amount = totalAmount.value;
        showMessage('error', 'ناتوانیت بڕێکی زیاتر لە کۆی گشتی بنووسی');
    }
});
const saveOrder = () => {
    // Set total and order_items before submission
    form.total = totalAmount.value;
    form.order_items = rows.value.map(item => ({
        id: item.id,
        price: item.price,
        quantity: item.quantity,
        rate_in: item.rate_in ?? 0,
    }));

    // Create a new form with JSON stringified order_items
    const formData = {
        ...form.data(),
        order_items: JSON.stringify(form.order_items),
        total: Number(totalAmount.value),
    };

    // Use router.post instead of form.post for better control
    if (form.status === 'loan' && form.customer_id === '') {
        showMessage('error', 'مادام قەرزە ناوێک هەڵبژێرە');
        return;
    }
    router.post(route('orders.store'), formData, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showMessage('success', 'بەسەرکەوتویی زیادکرا');
            form.reset();
            rows.value = [];
        },
        onError: () => {
            showMessage('error', 'هەڵەیەک ڕویدا لە کاتی پاشەکەوتکردن.');
        }
    });
};

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
        <div class="flex gap-4 items-center justify-between p-4 border border-gray-200 rounded-md mb-4 ">
            <input type="search" class="flex-1" placeholder="گەڕان بەپێی ناو یان کۆد ..." v-model="search">
            <Link :href="route('orders.index')" class="primary-btn">
                <span>گەڕانەوە</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18"/>
                </svg>
            </Link>
        </div>
        <hr class="dark:text-gray-100">
        <div class="mt-4 p-4 border border-gray-200 rounded-md">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                <div class="flex flex-col h-full p-4 border border-gray-400 rounded-md text-center" v-for="item in props.products.data" :key="item.id">
                    <div class="space-y-2 flex-grow dark:text-white mb-4">
                        <h5 class="text-xl"> نرخ / <span class="text-red-500">{{ formatNumber(item.price) }}</span></h5>
                        <p>{{ item.name }}</p>
                        <p>کۆد / {{ item.code }}</p>
                        <p>بڕی ماوە / <span class="text-red-500">{{ item.quantity }}</span></p> <!-- Fixed class name -->
                    </div>
                    <button class="primary-btn mt-auto flex items-center justify-center gap-2" @click="addRow(item)" :disabled="item.quantity<1">
                        <span>زیادکردن</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-plus" viewBox="0 0 16 16">
                            <path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9z"/>
                            <path
                                d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <PaginationLinks :paginator="products"/>
        </div>
        <hr class="my-5 dark:text-gray-100">
        <div v-if="rows.length > 0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-2 my-3">
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-white">ناوی کڕیار</label>
                    <SearchableSelect
                        v-model="form.customer_id"
                        :options="customers"
                        placeholder="ناوی کڕیار"
                        label="name"
                        value-prop="id"
                        no-options-text="هیچ کڕیارێک نەدۆزرایەوە"
                    />
                </div>
                <TextInput
                    type="number"
                    :thousand-separator="true"
                    v-model.number="form.paid_amount"
                    :max="totalAmount"

                    name="بڕی پارەی وەرگیراو"
                    min="0"
                    :disabled="totalAmount === 0"
                    placeholder="بڕی پارەی وەرگیراو بنوسە"
                />
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-white">جۆری فرۆشتن (قەرز/نەقد)</label>
                    <select id="status" v-model="form.status" class="w-full px-3 py-2 border border-blue-500 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled>جۆری مامەڵە</option>
                        <option value="cash">نەقد</option>
                        <option value="loan">قەرز</option>
                    </select>
                </div>
                <TextInput v-model="form.note" name="تێبینی" class="xl:col-span-2"/>
                <div class="flex items-center justify-around">
                    <h2 class="text-end text-red-500 text-2xl">{{ formatNumber(totalAmount) }}</h2>
                    <button class="primary-btn md:px-4" @click="saveOrder">فرۆشتن</button>
                </div>
            </div>
            <div class="row my-2 mx-auto">
                <div class="card-body">
                    <div class="overflow-x-auto mt-2">
                        <table>
                            <thead>
                            <tr>
                                <th>کۆد</th>
                                <th>ناوی کاڵا</th>
                                <th>نرخ</th>
                                <th>بڕی کاڵا</th>
                                <th>کۆ</th>
                                <th>سڕینەوە</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item, index) in rows" :key="index">
                                <td>{{ item.code }}</td>
                                <td>{{ item.name }}</td>
                                <td>
                                    <input type="number" min="250" step="250" class="text-center" v-model.number="item.price"/>
                                </td>
                                <td>
                                    <input type="number" min="1" :max="item.stock" class="text-center" v-model.number="item.quantity"/>
                                </td>
                                <td>
                                    {{ item.quantity && item.price ? formatNumber(item.quantity * item.price) : 0 }}
                                </td>
                                <td>
                                    <button class="text-white rounded-md bg-red-500 hover:bg-red-600 px-3 py-1 cursor-pointer flex items-center justify-center mx-auto" @click="deleteRow(index)">
                                        <!-- Fixed parameter -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                            <path
                                                d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                            <path
                                                d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <h3 class="text-center text-2xl dark:text-white">کاڵایەک هەڵبژێرە تا بیفرۆشیت.</h3>
        </div>
    </div>
</template>
