<script setup>
import {onMounted, ref, watch} from "vue";
import {useForm} from "@inertiajs/vue3";
import Swal from "sweetalert2";

const props = defineProps({
    all_permissions: {
        type: Array,
        required: true,
        default: () => []
    },
    permissions: {
        type: Array,
        default: () => []
    },
    role: {
        type: Object,
        required: true
    }
})

const selectedPermissions = ref([]);

const form = useForm({
    permissions: [],
    role: props.role
});

onMounted(() => {
    if (props.permissions && Array.isArray(props.permissions)) {
        selectedPermissions.value = props.permissions.map((p) => p.id);
        form.permissions = [...selectedPermissions.value]; // Initialize form with current permissions
    }
});

watch(selectedPermissions, (newValue) => {
    form.permissions = [...newValue];
}, {deep: true});

const submitForm = () => {
    form.put('/assign_permissions_to_roles', {
        onSuccess: () => {
            Swal.fire({
                position: "top-end",
                icon: "success",
                title: "بەسەرکەوتویی زیادکرا!",
                showConfirmButton: false,
                timerProgressBar: true,
                timer: 2000
            });
        }
    })
}
</script>

<template>
    <div class="container mx-auto">
        <h1 class="text-sm lg:text-xl border border-gray-200 rounded-md p-4 mt-2">پێدانی ڕێگەپێدان بە ڕۆڵ</h1>
        <div class="flex items-center justify-center flex-col border border-gray-200 rounded-md mt-4 p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-8 gap-5">
                <div v-for="permission in props.all_permissions" :key="permission.id" class="flex items-center">
                    <input
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 focus:ring-2"
                        type="checkbox" :value="permission.id"
                        :id="`permission-${permission.id}`"
                        v-model="selectedPermissions"
                    />
                    <label class="ms-2 font-medium text-gray-900" :for="`permission-${permission.id}`">
                        {{ permission.label }}
                    </label>
                </div>
            </div>
            <button type="button" class="primary-btn mt-6" @click="submitForm">
                خەزنکردن
            </button>
        </div>
    </div>
</template>
