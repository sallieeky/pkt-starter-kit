import { usePage } from "@inertiajs/vue3";

// can : should have the permission
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

// canAny : should have any of the permission
export function canAny(permission){
    if (typeof permission === 'string') {
        var permissionArray = permission.split('|');
        return permissionArray.reduce((res, permissionCursor) => res || usePage().props.auth.user_permissions.includes(permissionCursor.trim()), false);
    } else if (Array.isArray(permission)){
        return permission.reduce((res, permissionCursor) => res || usePage().props.auth.user_permissions.includes(permissionCursor.trim()), false);
    } else{
        return true;
    }
}

// canAll : should have all of the permission
export function canAll(permission){
    if (typeof permission === 'string') {
        var permissionArray = permission.split('|');
        return permissionArray.reduce((res, permissionCursor) => res && usePage().props.auth.user_permissions.includes(permissionCursor.trim()), true);
    } else if (Array.isArray(permission)){
        return permission.reduce((res, permissionCursor) => res && usePage().props.auth.user_permissions.includes(permissionCursor.trim()), true);
    } else{
        return true;
    }
}