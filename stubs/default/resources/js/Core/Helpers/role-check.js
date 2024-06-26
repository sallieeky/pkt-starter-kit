import { usePage } from "@inertiajs/vue3";

// hasRole : should have the role
export function hasRole(role){
    if (typeof role === 'string') {
        var roleArray = role.split('|');
        return roleArray.reduce((res, roleCursor) => res || usePage().props.auth.user_roles.includes(roleCursor.trim()), false);
    } else if (Array.isArray(role)){
        return role.reduce((res, roleCursor) => res || usePage().props.auth.user_roles.includes(roleCursor.trim()), false);
    } else{
        return true;
    }
}

// hasAnyRole : should have any of the role
export function hasAnyRole(role){
    if (typeof role === 'string') {
        var roleArray = role.split('|');
        return roleArray.reduce((res, roleCursor) => res || usePage().props.auth.user_roles.includes(roleCursor.trim()), false);
    } else if (Array.isArray(role)){
        return role.reduce((res, roleCursor) => res || usePage().props.auth.user_roles.includes(roleCursor.trim()), false);
    } else{
        return true;
    }
}

// hasAllRoles : should have all of the role
export function hasAllRole(role){
    if (typeof role === 'string') {
        var roleArray = role.split('|');
        return roleArray.reduce((res, roleCursor) => res && usePage().props.auth.user_roles.includes(roleCursor.trim()), true);
    } else if (Array.isArray(role)){
        return role.reduce((res, roleCursor) => res && usePage().props.auth.user_roles.includes(roleCursor.trim()), true);
    } else{
        return true;
    }
}
