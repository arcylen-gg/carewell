<template>
	<div class="dashboard">
		<v-breadcrumbs :items="breadcrumbs" class="px-0">
			<v-icon slot="divider">chevron_right</v-icon>
		</v-breadcrumbs>
		<v-layout row wrap>
			<v-flex lg4 sm6 xs12>
				<v-card>
					<v-card-text class="pa-0">
						<v-container class="pa-0">
						<div class="layout row ma-0" @click="go_to_url('/member-center')">
							<div class="sm6 xs6 flex">
								<div class="layout column ma-0 justify-center align-center">
									<v-icon size="56px" color="primary">fa-users</v-icon>
								</div>
							</div>
								<div class="sm6 xs6 flex text-sm-center py-3" style="background-color: #017ACB; color: white;">
								<div class="headline">{{ number_format(count.ctr_member) }}</div>
								<span class="caption">Members</span>
							</div>
						</div>
						</v-container>
					</v-card-text>
				</v-card>
			</v-flex>
			<v-flex lg4 sm6 xs12>
				<v-card>
					<v-card-text class="pa-0">
						<v-container class="pa-0">
							<div class="layout row ma-0" @click="go_to_url('/company-center')">
								<div class="sm6 xs6 flex">
									<div class="layout column ma-0 justify-center align-center">
										<v-icon size="56px" color="primary">fa-building</v-icon>
									</div>
								</div>
								<div class="sm6 xs6 flex text-sm-center py-3" style="background-color: #5939B4; color: white;">
									<div class="headline">{{ number_format(count.ctr_company) }}</div>
									<span class="caption">Companies</span>
								</div>
							</div>
						</v-container>
					</v-card-text>
				</v-card>
			</v-flex>
			<v-flex lg4 sm6 xs12>
				<v-card>
					<v-card-text class="pa-0">
						<v-container class="pa-0">
							<div class="layout row ma-0" @click="go_to_url('/settings/network-provider-center')">
								<div class="sm6 xs6 flex">
									<div class="layout column ma-0 justify-center align-center">
										<v-icon size="56px" color="primary">mdi-access-point-network</v-icon>
									</div>
								</div>
								<div class="sm6 xs6 flex text-sm-center py-3" style="background-color: #0194A8; color: white;">
									<div class="headline">{{ number_format(count.ctr_provider) }}</div>
									<span class="caption">Network Providers</span>
								</div>
							</div>
						</v-container>
					</v-card-text>
				</v-card>
			</v-flex>
		</v-layout>
	</div>
</template>

<script>
export default {
    data: () => ({
		breadcrumbs: [{
			text: 'Dashboard',
			disabled: false,
			href: '/'
		}, {
			text: 'Home',
			disabled: true,
		}],
	}),
	created(){
		this.get_count();
	}
}
</script>

<style lang="scss">
  @import "../scss/app.scss";

  .v-breadcrumbs li a {
    font-size: 16px;
  }
</style>