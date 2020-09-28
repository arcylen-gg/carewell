<template>
    <v-navigation-drawer temporary v-model="show" app>
        <v-toolbar color="primary darken-1" dark flat>
            <v-toolbar-title class="ml-0 pl-3">
                <img class="d-flex" height="35" src="../assets/drawer-logo.png" alt="">
            </v-toolbar-title>
        </v-toolbar>
        <navigation-drawer-list />
    </v-navigation-drawer>
</template>
<script>
    export default {
        data: () => ({
            show: false
        }),
        props: {
            drawer: {
                type: Boolean,
                default: true
            }
        },
        watch: {
            drawer: function (val) 
            {
                this.show = val;
            },
            show: function (val)
            {
                this.$emit("closeNav", val);
            }
        },
        mounted()
        {
            this.show = this.drawer;
        }
    }
</script>
<style lang="scss">
    @import "../scss/app.scss";
</style>