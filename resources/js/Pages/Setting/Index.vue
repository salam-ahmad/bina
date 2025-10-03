<script setup>
const props = defineProps({
    setting: {
        type: Object,
        required: true
    },
})

</script>

<template>
    <div class="container mx-auto mt-2">
        <Head title=" زانیاری دوکان"/>
        <div class="flex items-center justify-between px-4 mb-4 border border-gray-200 rounded-md p-4">
            <h1 class="text-sm lg:text-xl">زانیاری دوکان</h1>
<!--            <Link class="primary-btn w-fit" :href="route('settings.create')">-->
<!--                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">-->
<!--                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>-->
<!--                </svg>-->
<!--                <span>زیادکردن</span>-->
<!--            </Link>-->
        </div>
        <div class="border border-gray-200 rounded-md mt-4 p-4">
            <div class="overflow-x-auto">
                <table>
                    <thead>
                    <tr>
                        <th>ناو</th>
                        <th>وەسف</th>
                        <th>ناونیشان</th>
                        <th>ژمارە مۆبایلی 1</th>
                        <th>ژمارە مۆبایلی 2</th>
                        <th>زیاتر</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in props.setting" :key="item.id">
                        <td>{{ item.name }}</td>
                        <td>{{ item.description }}</td>
                        <td>{{ item.address }}</td>
                        <td>{{ item.phone_1 }}</td>
                        <td>{{ item.phone_2 }}</td>
                        <td>
                            <Link :href="route('settings.show',{id:item.id})" class="table_btn">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                </svg>
                                <span>زیاتر</span>
                            </Link>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
