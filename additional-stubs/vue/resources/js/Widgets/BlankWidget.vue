<template>
    <div class="p-4 rounded-xl border border-gray-400 flex-1">
        <div class="flex flex-row justify-between mb-4">
            <div>
                <div class="font-bold">WidgetName</div>
                <div class="font-thin text-gray-700 text-sm">Subtitle for your widget</div>
            </div>
        </div>
        <div>
            <h1>WidgetName</h1>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    // ...
});

</script>
