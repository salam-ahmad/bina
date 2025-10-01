<script setup>
defineProps({
    paginator: {
        type: Object,
        required: true,
    },
});

const makeLabel = (label) => {
    if (label.includes("Previous")) {
        return "<<";
    } else if (label.includes("Next")) {
        return ">>";
    } else {
        return label;
    }
};
</script>

<template>
    <div class="flex justify-between items-center">
        <div class="flex items-center rounded-md overflow-hidden shadow-lg">
            <div v-if="paginator?.links?.length" v-for="(link, index) in paginator.links" :key="index">
                <component
                    :is="link.url ? 'Link' : 'span'"
                    :href="link.url"
                    preserve-state
                    preserve-scroll
                    v-html="makeLabel(link.label)"
                    class="border-x border-slate-50 md:w-12 md:h-12 w-8 h-8  grid place-items-center bg-white dark:bg-gray-700 dark:text-green-500 dark:border-green-500"
                    :class="{
            'hover:bg-green-200': link.url,
            'text-green-400': !link.url,
            'font-bold text-green-500': link.active,
          }"
                />
            </div>
        </div>

        <p class="text-slate-600 md:text-sm dark:text-slate-100 text-xs">
            پیشاندانی {{ paginator.from }} بۆ {{ paginator.to }} لەکۆی {{ paginator.total }}
        </p>
    </div>
</template>
