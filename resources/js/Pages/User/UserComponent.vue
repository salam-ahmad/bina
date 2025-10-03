<script setup>
import PaginationLinks from "@/Pages/Components/PaginationLinks.vue";
import {ref, watch} from "vue";
import {Link, router} from "@inertiajs/vue3";
import {debounce} from "lodash";
import {getDate} from "@/utils/numberUtils.js";

import {usePermissions} from '@/composables/usePermissions';

const {hasPermission} = usePermissions();

const props = defineProps({
    users: Object,
    searchTerm: String,
})

const search = ref(props.searchTerm);

watch(
    search,
    debounce((q) => router.get(route('users.index'), {search: q}, {preserveState: true}), 500)
);
</script>

<template>
    <Head title="- ماڵەوە"/>
    <div class="container mx-auto mt-2">
        <div class="flex items-center justify-between px-4 mb-4 border border-gray-200 rounded-md p-4">
            <h1 class="text-sm lg:text-xl">لیستی بەکارهێنەران</h1>
            <div class="flex items-center justify-between gap-4">
                <input type="search" placeholder="گەڕان ..." v-model="search"/>
                <Link :href="route('edit_password')" class="primary-btn" method="get">وشەتێپەڕ</Link>
                <Link :href="route('register')" class="primary-btn  min-w-fit" v-if="hasPermission('user_add')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <span>زیادکردن</span>
                </Link>
            </div>
        </div>
        <!-- v-if="hasPermission('user_list')"-->
        <div class="border border-gray-200 rounded-md mt-4 p-4">
            <div class="overflow-x-auto">
                <table class="mt-2 text-center">
                    <thead>
                    <tr>
                        <th>ناو</th>
                        <th>ئیمەیڵ</th>
                        <th>ڕۆڵ</th>
                        <th>ڕێکەوتی دروستکردن</th>
                        <th>گۆڕین</th>
                        <!--v-if="hasPermission('user_edit')"-->
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="user in users.data" :key="user.id">
                        <td>{{ user.name }}</td>
                        <td>{{ user.email }}</td>
                        <td>{{ user.role.label }}</td>
                        <td>{{ getDate(user.created_at) }}</td>
                        <td>
                            <!--v-if="hasPermission('user_edit')"-->
                            <Link :href="route('users.edit',{id:user.id})" class="table_btn">
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
                <div>
                    <PaginationLinks :paginator="users"/>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
