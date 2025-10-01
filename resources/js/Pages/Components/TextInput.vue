<script setup>
const model = defineModel({
    type: null,
    required: true
})
const props = defineProps({
    name: {
        type: String,
        required: true
    },
    type: {
        type: String,
        default: 'text'
    },
    message: String,
    thousandSeparator: {
        type: Boolean,
        default: false
    },
    readonly: {
        type: Boolean,
        default: false
    }
})
const formatNumber = (value) => {
    if (!value) return '';
    const numericValue = value.toString().replace(/[^\d.]/g, '');
    return numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
};

const handleInput = (event) => {
    if (props.thousandSeparator) {
        // Only format if thousandSeparator is true
        const rawValue = event.target.value.replace(/,/g, '');
        const formattedValue = formatNumber(rawValue);

        event.target.value = formattedValue;
        model.value = rawValue;
    } else {
        model.value = event.target.value;
    }
};
</script>

<template>
    <div class="mb-6">
        <label class="dark:text-white ">{{ name }} </label>
        <input
            :type="thousandSeparator ? 'text' : type"
            :inputmode="(type === 'number' || thousandSeparator) ? 'numeric' : undefined"
            :readonly="readonly"
            :value="thousandSeparator ? formatNumber(model) : model"
            @input="handleInput"
            :class="{ '!border-red-500': message }"
            :min="$attrs.min"
            :max="$attrs.max"
            :placeholder="$attrs.placeholder"
            :disabled="$attrs.disabled"
         />

        <small class="error" v-if="message">{{ message }}</small>
    </div>
</template>

