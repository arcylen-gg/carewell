<template>
    <v-list dense class="py-0" dark>
        <v-list-tile v-for="page in pages" :key="page.name" :style="{backgroundColor: page.color}" v-if="!page.children.length &&  page.parent_id == 0 && page.accesses.read"  :to="page.url"  @click="checkRoute(page.accesses.read)">
            <v-list-tile-action>
                <v-icon :style="{ color: page.text_color }">{{ page.icon }}</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
                <v-list-tile-title :style="{ color: page.text_color }">{{ page.name }}</v-list-tile-title>
            </v-list-tile-content>
        </v-list-tile>
        <v-list class="py-0" v-for="page in pages" :key="page.name" :style="{backgroundColor: page.color}" v-if="page.children.length &&  page.parent_id == 0 && page.accesses.read">
            <v-list-group dark no-action>
                <v-list-tile slot="activator" dark>
                    <v-list-tile-action>
                        <v-icon>settings</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ page.name }}</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
                <sub-pages :children="page.children"></sub-pages>
                
            </v-list-group>
        </v-list>
    </v-list>
</template>

<script>
	export default {
		data: () => ({
			
        }),
        mounted () {
        },
        methods : {
            checkRoute(view){
                if(!view)
                {
                    this.$router.push('/error')
                }
			}
        }

	}
</script>
<style lang="scss">
    @import "../scss/app.scss";
    .v-list-tile-title
    {
        text-decoration: none !important;
    }
</style>
