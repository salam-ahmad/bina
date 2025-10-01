<script setup>
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import {computed} from "vue";
import 'vue-select/dist/vue-select.css'

const props = defineProps({
    modelValue: {
        type: [String, Number, Object],
        default: null
    },
    options: {
        type: [Array, Object],
        required: true
    },
    label: {
        type: String,
        default: 'name' // Default property to display
    },
    valueProp: {
        type: String,
        default: 'id' // Default property to use as value
    },
    placeholder: {
        type: String,
        default: 'Select an option'
    },
    searchable: {
        type: Boolean,
        default: true
    },
    clearable: {
        type: Boolean,
        default: true
    },
    disabled: {
        type: Boolean,
        default: false
    },
    error: {
        type: String,
        default: null
    },
    required: {
        type: Boolean,
        default: false
    },
    noOptionsText: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue'])

const updateValue = (value) => {
    emit('update:modelValue', value)
}

// Convert options to array if it's an object
const optionsList = computed(() => {
    if (Array.isArray(props.options)) {
        return props.options
    }
    // If it's an object, convert to array
    return Object.values(props.options)
})
</script>

<template>
    <div class="w-full">
        <v-select
            :model-value="modelValue"
            @update:model-value="updateValue"
            :options="optionsList"
            :label="label"
            :reduce="item => item[valueProp]"
            :placeholder="placeholder"
            :searchable="searchable"
            :clearable="clearable"
            :disabled="disabled"
            class="w-full border-indigo-500 border-1 rounded-md dark:border-gray-200  dark:bg-gray-800 dark:text-white"
            :class="{ 'border-red-500': error }">
            <template #no-options="{ search }">
                <div class="text-center py-2 text-red-500">
                    {{ props.noOptionsText }}
                </div>
            </template>
        </v-select>
        <small class="error text-red-500 text-sm mt-1" v-if="error">{{ error }}</small>
    </div>
</template>
