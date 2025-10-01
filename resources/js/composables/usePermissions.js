import {computed} from 'vue';
import {usePage} from '@inertiajs/vue3';

export function usePermissions() {
    const {props} = usePage();

    const permissions = computed(() => props.auth.permissions || []);

    const hasPermission = (permission) => {
        return permissions.value.includes(permission);
    };

    const hasAnyPermission = (permissionArray) => {
        return permissionArray.some(permission => permissions.value.includes(permission));
    };

    const hasAllPermissions = (permissionArray) => {
        return permissionArray.every(permission => permissions.value.includes(permission));
    };

    return {
        permissions,
        hasPermission,
        hasAnyPermission,
        hasAllPermissions
    };
}
