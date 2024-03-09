export const navItems = [
    {
        label: "Dashboard",
        href: "/dashboard",
        icon: "home"
    },
    {
        label: "Master Data",
        href: "/master",
        icon: "square-3-stack-3d",
        submenu:[
            {
                label: "Data 1",
                href: "/master/data-1",
            },
            {
                label: "Data 2",
                href: "/master/data-2",
            },
        ]
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