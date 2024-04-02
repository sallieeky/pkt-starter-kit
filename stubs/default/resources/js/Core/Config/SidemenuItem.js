export const navItems = [
    {
        label: "Dashboard",
        href: "/dashboard",
        icon: "home"
    },
    {
        label: "User Management",
        href: "/users",
        icon: "users",
        permission: "user.browse | role.browse",
        submenu:[
            {
                label: "User",
                href: "/user",
                permission: "user.browse",
            },
            {
                label: "Role & Permission",
                href: "/role-and-permission",
                permission: "role.browse",
            },
            {
                label: "User Log",
                href: "/user-log",
            },
        ]
    },
];