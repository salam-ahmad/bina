<script setup>
import {useForm, Link} from '@inertiajs/vue3'
import {computed} from 'vue'
import TextInput from "@/Pages/Components/TextInput.vue";

const props = defineProps({
    order: {
        type: Object,
        required: true
    },
    flash: Object
})

const form = useForm({
    amount: '',
    note: ''
})

const canSubmit = computed(() => {
    const amt = Number(form.amount || 0)
    return amt > 0 && amt <= Number(props.order.due_amount || 0)
})

const submit = () => {
    form.post(route('payments.receiveForOrder', props.order.id))
}
</script>

<template>
    <div class="container mx-auto p-4">
        <h1 class="text-xl font-bold mb-4">وەرگرتنی پارە (پسوڵەی ژمارە {{ order.id }})</h1>

        <div class="rounded border p-3 mb-4 bg-white dark:bg-gray-800 dark:border-gray-200">
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div class="dark:text-white text-lg"><span>کڕیار : </span> {{ order.customer ?? '—' }}</div>
                <div class="dark:text-white text-lg"><span> Status:</span> {{ order.status }}</div>
                <div class="dark:text-white text-lg"><span>کۆی گشتی : </span> {{ Number(order.total).toLocaleString() }}</div>
                <div class="dark:text-white text-lg"><span>بڕی دراو : </span> {{ Number(order.paid_amount).toLocaleString() }}</div>
                <div class="col-span-2 dark:text-white text-lg">
                    <span>بڕی ماوە : </span>
                    <span class="text-red-500">{{ Number(order.due_amount).toLocaleString() }}</span>
                </div>
            </div>
        </div>


        <form @submit.prevent="submit" class="space-y-3 bg-white border rounded p-3 dark:bg-gray-800">
            <div class="">
                <label class="block text-sm font-medium dark:text-gray-200">بڕی پارە (د.ع)</label>
                <TextInput
                    type="number"
                    :thousand-separator="true"
                    v-model.number="form.amount"
                    :max="order.due_amount"
                    class="w-full  p-2"
                    name="بڕی پارەی وەرگیراو"
                    min="1"
                    placeholder="بۆ نمونە 20,000"
                />
                <div v-if="form.errors.amount" class="text-red-600 text-sm mt-1">
                    {{ form.errors.amount }}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1 dark:text-gray-200">تێبینی</label>
                <textarea v-model="form.note" class="w-full border rounded p-2" rows="3"
                          placeholder="ئەگەر تێبینیەک هەیە بینوسە"></textarea>
                <div v-if="form.errors.note" class="text-red-600 text-sm mt-1">
                    {{ form.errors.note }}
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="submit"
                    :disabled="form.processing || !canSubmit"
                    class="primary-btn py-2"
                >
                    وەرگرتن
                </button>
<!--                <Link href="#" onclick="history.back()" class="px-4 py-2 rounded border">وازهێنان</Link>-->
            </div>

            <p v-if="form.recentlySuccessful || $page.props.flash?.success" class="text-green-700">
                {{ $page.props.flash?.success }}
            </p>
        </form>
    </div>
</template>
