import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useSidemenuStore = defineStore('sidemenu', () => {
    const sidemenu = ref(false)
    const drawer = ref(false)

    function toggleSidemenu() {
        sidemenu.value = !sidemenu.value
    }
    function toggleDrawer() {
        drawer.value = !drawer.value
    }
    function showSidemenu(){
        sidemenu.value = false
    }
    function hideDrawer(){
        drawer.value = false
    }

    return { sidemenu, drawer, toggleSidemenu, toggleDrawer, showSidemenu, hideDrawer }
});