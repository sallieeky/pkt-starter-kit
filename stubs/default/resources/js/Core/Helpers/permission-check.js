import { usePage } from "@inertiajs/vue3";

export function can(permission){
    if (typeof permission === 'string') {
        var permissionArray = permission.split('|');
        return permissionArray.reduce((res, permissionCursor) => res || usePage().props.auth.user_permissions.includes(permissionCursor.trim()), false);
    } else if (Array.isArray(permission)){
        return permission.reduce((res, permissionCursor) => res || usePage().props.auth.user_permissions.includes(permissionCursor.trim()), false);
    } else{
        return true;
    }
}
