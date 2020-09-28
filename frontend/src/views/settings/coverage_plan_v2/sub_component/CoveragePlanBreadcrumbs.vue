<template>
    <v-breadcrumbs :items="breadcrumbs" class="px-0">
        <v-icon slot="divider">chevron_right</v-icon>
    </v-breadcrumbs>
</template>

<script>
export default {
    data: () => ({
        breadcrumbs: [
            { text: 'Settings', disabled: false }, 
            { text: 'Coverage Plan', disabled: false }, 
            { text: 'Create', disabled: true } 
        ],
    }),
}
</script>
