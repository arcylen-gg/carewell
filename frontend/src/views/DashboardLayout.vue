<template>
	<v-app id="inspire">
		<navigation-drawer :drawer="drawer" @closeNav="closeNavigation"></navigation-drawer>
		<v-toolbar color="primary" dark fixed app flat>
			<v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
			<v-spacer></v-spacer>
			<v-menu offset-y origin="center center" :nudge-bottom="10" transition="scale-transition">
				<v-btn icon large flat slot="activator">
					<v-avatar size="30px">
						<img src="../assets/profile-img.jpg"/>
					</v-avatar>
				</v-btn>
				<v-list class="pa-0">
					<v-list-tile rel="noopener" style="cursor: pointer;">
						<v-list-tile-action>
							<v-icon>mdi-account</v-icon>
						</v-list-tile-action>
						<v-list-tile-content>
							<account-settings>Profile</account-settings>
						</v-list-tile-content>
					</v-list-tile>
					<v-list-tile @click.native="logout" rel="noopener">
						<v-list-tile-action>
							<v-icon>mdi-logout</v-icon>
						</v-list-tile-action>
						<v-list-tile-content>
							<v-list-tile-title>Logout</v-list-tile-title>
						</v-list-tile-content>
					</v-list-tile>
				</v-list>
			</v-menu>
		</v-toolbar>
		<v-content style="background-color: #F0F3F6">
			<v-container grid-list-xl fluid>
				<router-view></router-view>
			</v-container>
		</v-content>
		<v-footer color="white" app inset>
			<span class="caption ml-3">&copy; Carewell Health System INC. All Right Reserved
			</span>
			<v-spacer></v-spacer>
			<span class="caption mr-3">Powered By: DIGIMA WEB SOLUTIONS, Inc.</span>
		</v-footer>
	</v-app>
</template>

<script>
	export default {
		data: () => ({
			drawer: false,
			db_routes: []
		}),
        created () 
        {	       
			
		},
		mounted() {
			if(!this.user)
			{
				this.$router.push('/login')
			}
			else
			{
				this.checkRoute()
			}
		},
		methods : {
			checkRoute(){
				this.pages.forEach((data, key) => {
					if(data.code == this.$route.name)
					{
						if(!data.accesses.read)
						{
                    		this.$router.push('/error')
						}
					}
				})
			},
			closeNavigation(value)
			{
				this.drawer = value
			}
		}
	}
</script>
<style lang="scss">
	@import "../scss/app.scss";
</style>