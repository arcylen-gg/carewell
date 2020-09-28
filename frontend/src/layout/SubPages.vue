<template>
    <span>
        <v-list-tile v-for="child in children" :key="child.name" v-if="!child.children.length && child.accesses.read" :to="child.url" >
            <v-list-tile-content>
                <v-list-tile-title>{{ child.name }}</v-list-tile-title>
            </v-list-tile-content>
        </v-list-tile>
        <v-list-group v-for="child in children" :key="child.name" sub-group no-action v-if="child.children.length && child.accesses.read">
            <v-list-tile slot="activator">
                <v-list-tile-title>{{ child.name }}</v-list-tile-title>
            </v-list-tile>
            <sub-pages :children="child.children"></sub-pages>
        </v-list-group>
    </span>
</template>
<script>
import Router from 'vue-router'

  export default {
    data: () => ({
    }),
    props: {
        children: Array,
    },
    mounted () {
    },
  }
</script>