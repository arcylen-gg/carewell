import Vue from 'vue'
import Router from 'vue-router'
import Home from './views/Home.vue'

Vue.use(Router)

export default new Router({
	mode: 'history',
	routes: [
		{ path: '/', name: 'home', component: Home, redirect: '/dashboard/home' },
		{ path: '/login', name: 'login', component: () => import('./views/Login.vue') },
		{ path: '/dashboard', name: 'dashboard-layout', redirect: '/dashboard/home', component: () => import('./views/DashboardLayout.vue'), children: [
			{ path: '/dashboard/home', name: 'dashboard', component: Home },
			{ path: '/company-center', name: 'company-center', component: () => import('./views/company_center/CompanyCenter.vue') },
			{ path: '/import-company-center', name:'import-company-center', component:() => import('./views/company_center/import/ImportCompanyPage.vue') },
			{ path: '/settings/doctor-center', name: 'doctor-center', component: () => import('./views/doctor_center/DoctorCenter.vue') },
			{ path: '/member-center', name: 'member-center', component: () => import('./views/member_center/MemberCenter.vue') },
			{ path: '/import-member-center', name: 'import-member-center', component: () => import('./views/member_center/import/ImportMemberPage.vue') },
			{ path: '/settings/import-network-provider-center', name: 'import-provider-center', component: () => import('./views/provider_center/import/ImportProviderPage.vue') },
			{ path: '/settings/network-provider-center', name: 'network-provider-center', component: () => import('./views/provider_center/ProviderCenter.vue') },
			{ path: '/settings/coverage-plan', name: 'coverage-plan', component: () => import('./views/settings/coverage_plan_v2/CoveragePlanCenter.vue') },
			{ path: '/settings/coverage-plan/create', name: 'coverage-plan-create', component: () => import('./views/settings/coverage_plan_v2/CreateCoveragePlan.vue') },
			{ path: '/settings/coverage-plan/edit', name: 'coverage-plan-edit', component: () => import('./views/settings/coverage_plan_v2/CreateCoveragePlan.vue') },
			{ path: '/settings/utilities/access-level', name: 'access-level', component: () => import('./views/settings/access_level/AccessLevel.vue') },
			{ path: '/settings/utilities/admin-panel', name: 'admin-panel', component: () => import('./views/settings/admin_panel/AdminPanel.vue') },
			{ path: '/settings/maintenance/coverage-plan-description', name: 'coverage-plan-description', component: () => import('./views/settings/maintenance/coverage_plan_description/CoveragePlanDescription.vue') },
			{ path: '/settings/maintenance/import-coverage-plan-description', name: 'import-coverage-plan-description', component: () => import('./views/settings/maintenance/coverage_plan_description/import/ImportPlanDescriptionPage.vue') },
			{ path: '/settings/maintenance/pre-existing', name: 'pre-existing', component: () => import('./views/settings/maintenance/pre_existing/PreExisting.vue') },
			{ path: '/settings/maintenance/procedure-types', name: 'procedure-types', component: () => import('./views/settings/maintenance/procedures_type/ProceduresType.vue') },
			{ path: '/settings/maintenance/diagnosis-list', name: 'diagnosis-list', component: () => import('./views/settings/maintenance/diagnosis_list/DiagnosisList.vue') },
			{ path: '/settings/maintenance/import-diagnosis-list', name: 'import-diagnosis-list', component: () => import('./views/settings/maintenance/diagnosis_list/import/ImportDiagnosis.vue') },
			{ path: '/settings/maintenance/payment-method', name: 'payment-method', component: () => import('./views/settings/maintenance/payment_method/PaymentMethod.vue') },
			{ path: '/settings/maintenance/specialization', name: 'specialization', component: () => import('./views/settings/maintenance/specialization/Specialization.vue') },
			{ path: '/error', name: 'error', component: () => import('./views/error/Error.vue') },
			{ path: '/file_upload', name: 'file_upload', component: () => import('./components/FileUploader.vue') },

			//Billing Center
			{ path: '/billing-&-collection-center', name: 'billing-&-collection-center', component: () => import('./views/billing_center/BillingCenter.vue') },
			{ path: '/billing-&-collection-center/create', name: 'billing-&-collection-center-create', component: () => import('./views/billing_center/page/CreateCollectionActiveList.vue') },
			{ path: '/billing-&-collection-center/edit', name: 'billing-&-collection-center-edit', component: () => import('./views/billing_center/page/EditCollectionActiveList.vue') },
			{ path: '/billing-&-collection-center/view', name: 'billing-&-collection-center-view', component: () => import('./views/billing_center/page/ViewCollectionActiveList.vue') },
			{ path: '/billing-&-collection-center/import', name: 'billing-&-collection-center-import', component: () => import('./views/billing_center/page/ImportCollectionActiveListMember.vue') },
			{ path: '/billing-&-collection-center/mark-close', name: 'billing-&-collection-center-mark-close', component: () => import('./views/billing_center/page/MarkCloseCollectionActiveList.vue') },
			{ path: '/billing-&-collection-center/mark-close-view', name: 'billing-&-collection-center-mark-close-view', component: () => import('./views/billing_center/page/ViewCollectionActiveListClose.vue') },
			//Availment Center
			{ path: '/availment-center', name: 'availment-center', component: () => import('./views/availment_center/AvailmentCenter.vue') },
			{ path: '/availment-center/create', name: 'availment-center-create', component: () => import('./views/availment_center/page/CreateAvailment.vue') },
			{ path: '/availment-center/view', name: 'availment-center-view', component: () => import('./views/availment_center/page/ViewAvailment.vue') },
			{ path: '/availment-center/edit', name: 'availment-center-edit', component: () => import('./views/availment_center/page/EditAvailment.vue') },
			{ path: '/availment-center/import', name: 'availment-center-import', component: () => import('./views/availment_center/page/imports/ImportAvailment.vue') },
			//Audit Trail
			{ path: '/settings/utilities/audit-trail', name: 'audit-trail', component: () => import('./views/settings/audit_trail/AuditTrail.vue') },
			//Bank
			{ path: '/settings/maintenance/bank', name: 'bank', component: () => import('./views/settings/maintenance/bank/Bank.vue') },
			//Payment Terms
			{ path: '/settings/maintenance/payment-term', name: 'payment-term', component: () => import('./views/settings/maintenance/payment_term/PaymentTerm.vue') },
			//Other Setting 
			//Accounting Transaction
			{ path: '/settings/utilities/other-setting', name: 'other-setting', component: () => import('./views/settings/other_setting/Setting.vue') },
			//Payable
			{ path: '/payable-center', name: 'payable-center', component: () => import('./views/payable_center/PayableCenter.vue') },
			{ path: '/payable-center/create', name: 'payable-center-create', component: () => import('./views/payable_center/popup/CreatePayable.vue') },
			{ path: '/payable-center/edit', name: 'payable-center-edit', component: () => import('./views/payable_center/popup/CreatePayable.vue') },
			{ path: '/payable-center/mark-close', name: 'payable-center-mark-close', component: () => import('./views/payable_center/popup/PayableMarkAsClose.vue') },
			{ path: '/payable-center/view', name: 'payable-center-view', component: () => import('./views/payable_center/popup/ViewPayable.vue') },
			//Report
			{ path: '/reports/availment-summary', name: 'availment-summary', component: () => import('./views/reports/availment_summary/AvailmentSummary.vue') },
			{ path: '/reports/company-availment', name: 'company-availment', component: () => import('./views/reports/company_availment/CompanyAvailment.vue') },
			{ path: '/reports/availment-monitoring', name: 'availment-monitoring', component: () => import('./views/reports/availment_monitoring/AvailmentMonitoring.vue') },
			{ path: '/reports/policy-data', name: 'policy-data', component: () => import('./views/reports/policy_data/PolicyData.vue') },
			{ path: '/reports/comparative-availment', name: 'comparative-availment', component: () => import('./views/reports/comparative_availment/ComparativeAvailment.vue') },
			{ path: '/reports/availment-per-type', name: 'availment-per-type', component: () => import('./views/reports/availment_per_type/AvailmentPerType.vue') },
			{ path: '/reports/availment-per-month-per-client', name: 'availment-per-month-per-client', component: () => import('./views/reports/availment_per_month_per_client/AvailmentPerMonthPerClient.vue') },
			{ path: '/reports/active-member-per-month', name: 'active-member-per-month', component: () => import('./views/reports/active_member_per_month/ActiveMemberPerMonth.vue') },
			//Import Specialization
			{ path: '/settings/maintenance/import-specialization', name: 'import-specialization', component: () => import('./views/settings/maintenance/specialization/import/ImportSpecialization.vue') },
			//Import Doctor
			{ path: '/settings/maintenance/import-doctor', name: 'import-doctor', component: () => import('./views/doctor_center/import/ImportDoctor.vue') },
		] },
		
	]
})