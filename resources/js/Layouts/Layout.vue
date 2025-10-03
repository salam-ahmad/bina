<script setup>
import {Link, usePage} from '@inertiajs/vue3'
import {ref, onMounted} from 'vue'
import ThemeToggle from "@/utils/ThemeToggle.vue";
import {usePermissions} from '@/composables/usePermissions';

const {hasPermission} = usePermissions();

const showSidebar = ref(false)

const toggleSidebar = () => {
    showSidebar.value = !showSidebar.value
}

const closeSidebar = () => {
    showSidebar.value = false
}

// Optional: Close sidebar on route change
onMounted(() => {
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            showSidebar.value = false
        }
    })
})


</script>

<template>
    <div class="lg:flex min-h-screen">
        <!-- Mobile Header -->
        <header class="bg-white dark:bg-gray-900 h-16 flex justify-between items-center lg:hidden w-full p-4">
            <span class="text-xl dark:text-gray-200">{{ $page.props.settings.name }}</span>
            <svg @click="toggleSidebar" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="cursor-pointer w-8 h-8 dark:text-gray-200">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
        </header>
        <!-- Sidebar Overlay on Mobile -->
        <div v-if="showSidebar" class="lg:hidden fixed inset-0 z-40 " @click="closeSidebar"></div>
        <!-- Sidebar -->
        <aside :class="['dark:bg-gray-800 flex flex-col justify-between fixed top-0 right-0 z-50 h-full w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out',
                        showSidebar ? 'translate-x-0' : 'translate-x-full',
                        'lg:translate-x-0 lg:static lg:w-64 lg:h-auto']" @click.stop>
            <div>
                <div class="pb-10 border-b border-b-gray-400 flex flex-col items-center gap-y-4 py-5 px-3 dark:text-white">
                    <Link href="/" class="text-2xl font-semibold" @click="closeSidebar">
                        <span class="text-center">{{ $page.props.settings.name }}</span>
                    </Link>
                    {{ $page.props.auth.user.name }}
                </div>
                <div class="flex flex-col justify-between ">
                    <div class="space-y-1 py-5 px-3 dark:text-white" v-if="$page.props.auth.user">
                        <!--<Link :href="route('home')" class="link" :class="{'active_link': $page.component.startsWith('Home')}" @click="closeSidebar">-->
                        <!-- داشبۆرد-->
                        <!--</Link>-->
                        <!--v-if="hasPermission('dashboard_view')"-->
                        <Link :href="route('dashboard.finance')" class="link" :class="{'active_link': $page.component.startsWith('Dashboard')}" @click="closeSidebar">
                            داشبۆرد
                        </Link>
                        <!--v-if="hasPermission('product_list')"-->
                        <Link :href="route('products.index')" class="link" :class="{'active_link': $page.component.startsWith('Product')}" @click="closeSidebar">
                            کاڵاکان
                        </Link>
                        <Link :href="route('permissions.index')" class="link" :class="{'active_link': $page.component.startsWith('Permission')}" @click="closeSidebar">
                            ڕێگەپێدان
                        </Link>
                        <!--v-if="hasPermission('role_list')"-->
                        <Link :href="route('roles.index')" class="link" :class="{'active_link': $page.component.startsWith('Role')}" @click="closeSidebar">
                            ڕۆڵ
                        </Link>
                        <!--v-if="hasPermission('customer_list')"-->
                        <Link :href="route('customers.index')" class="link" :class="{'active_link': $page.component.startsWith('Customer')}" @click="closeSidebar">
                            کڕیارەکان
                        </Link>
                        <!--v-if="hasPermission('supplier_list')"-->
                        <Link :href="route('suppliers.index')" class="link" :class="{'active_link': $page.component.startsWith('Supplier')}" @click="closeSidebar"> فرۆشیارەکان</Link>
                        <!--v-if="hasPermission('order_list')"-->
                        <Link :href="route('orders.index')" class="link" :class="{'active_link': $page.component.startsWith('Order') }" @click="closeSidebar"> فرۆشتن</Link>
                        <!--v-if="hasPermission('purchase_list')"-->
                        <Link :href="route('purchases.index')" class="link" :class="{'active_link': $page.component.startsWith('Purchase')}" @click="closeSidebar"> کڕین</Link>
                        <Link :href="route('units.index')" class="link" :class="{'active_link': $page.component.startsWith('Unit')}" @click="closeSidebar"> پێوانەکان</Link>
                        <Link :href="route('currencies.index')" class="link" :class="{'active_link': $page.component.startsWith('Currency')}" @click="closeSidebar"> دراو</Link>
                        <Link :href="route('users.index')" class="link" :class="{'active_link': $page.component.startsWith('User')}" @click="closeSidebar">
                            بەکارهێنەران
                        </Link>
                        <!--v-if="hasPermission('shop_view')"-->
                        <Link :href="route('settings.index')" class="link" :class="{'active_link': $page.component.startsWith('Setting')}" @click="closeSidebar">
                            دوکان
                        </Link>

                    </div>
                </div>
            </div>
            <div class="p-5 m-5 flex items-center justify-between">
                <div v-if="$page.props.auth.user">
                    <Link :href="route('logout')" class="link cursor-pointer bg-green-50 text-green-600 border-1 border-green-600" method="post">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="size-5" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                  d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z"/>
                            <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z"/>
                        </svg>
                        <span>دەرچون</span>
                    </Link>
                </div>
                <div>
                    <ThemeToggle/>
                </div>
            </div>
        </aside>
        <div v-if="showSidebar" class="lg:hidden fixed inset-0 z-40 bg-gray-50 bg-opacity-30" @click="closeSidebar"></div>
        <!-- Main Content -->
        <main class="flex-1 p-2 min-h-screen dark:bg-gray-900">
            <slot/>
        </main>
    </div>
</template>

