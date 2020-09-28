import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const availmentData = new Vuex.Store({
    state:{
        form : {
            getSelectedMember : {},
            getSelectedMemberBenefitType : {},
            getSelectedMemberAvailment : [],

            getSelectedProvider : {},

            getTotalProcedureCarewell : 0.00,
            getTotalPhysicianCarewell : 0.00,
            amount_covered : 0,
            remaining_balance : 0,
            // callerInformation : {},
            // memberInformation : {},
            // availmentInformation : {},
            // payeeInformation : {},
            physicianInformation : [],
            procedureInformation : [],
            getMemberAvailmentByBenefit: {},
            viewingAvailment :  true,
        },
    },
    getters:{
        getMemberBenefitTypeList : state => {
            return state.form.getSelectedMember.hasOwnProperty('benefit_type') ? state.form.getSelectedMember.benefit_type : [];
        },
        getMemberProcedureList : state => {
            return state.form.getSelectedMemberBenefitType.hasOwnProperty('procedure') ? state.form.getSelectedMemberBenefitType.procedure : [];
        },
        getProviderDoctorList : state => {
            return state.form.getSelectedProvider.hasOwnProperty('doctor_providers') ? state.form.getSelectedProvider.doctor_providers : [];
        },
        getProviderPayeeList : state => {
            return state.form.getSelectedProvider.hasOwnProperty('provider_payee') ? state.form.getSelectedProvider.provider_payee : [];
        },
        getSelectedMemberAvailment : state => {
            return state.form.getSelectedMemberAvailment ? state.form.getSelectedMemberAvailment : [] ;
        },
        physicianInformation : state => {
            return state.form.physicianInformation ? state.form.physicianInformation : [];
        },
        viewingAvailment : state => {
            return state.form.viewingAvailment ;
        },
        procedureInformation : state => {
            return state.form.procedureInformation ? state.form.procedureInformation : [];
        },
        maximumBenefitLimit : state => {
            return state.form.getSelectedMember.hasOwnProperty('company') ? state.form.getSelectedMember.company.coverage_plan.maximum_benefit_limit : 0;
        },
    },
    mutations:{
        getSelectedMember : (state, member) => {
            state.form.getSelectedMemberBenefitType = new Object;
            state.form.getSelectedMember = member;
        },
        getSelectedProvider : (state, provider) => {
            state.form.getSelectedProvider = provider;
        },
        getSelectedMemberBenefitType : (state, benefitType) => {
            state.form.getSelectedMemberBenefitType = benefitType;
        },
        getTotalPhysicianCarewell : (state, total) => {
            state.form.getTotalPhysicianCarewell = total;
        },
        getTotalProcedureCarewell : (state, total) => {
            state.form.getTotalProcedureCarewell = total;
        },
        getSelectedMemberAvailment : (state, availment) => {
            state.form.getSelectedMemberAvailment = availment;
        },
        physicianInformation : (state, physicianList) => {
            state.form.physicianInformation = physicianList;
        },
        viewingAvailment : (state, value)=> {
            state.form.viewingAvailment = value;
        },
        procedureInformation : (state, procedureList) => {
            state.form.procedureInformation = procedureList;
        },
        amount_covered : (state, value) => {
            state.form.amount_covered = value;
        },
        remaining_balance : (state, value) => {
            state.form.remaining_balance = value;
        },
        getMemberAvailmentByBenefit : (state, availRecord) => {
            state.form.getMemberAvailmentByBenefit = availRecord;
        }
    },
    actions:{
        getSelectedMember : async ( context, value ) => {
            await context.commit('getSelectedMember',value);
        },
        getSelectedProvider : async ( context, value ) => {
            await context.commit('getSelectedProvider',value);
        },
        getSelectedMemberBenefitType : async ( context, value ) => {
            await context.commit('getSelectedMemberBenefitType',value);
        },
        getTotalPhysicianCarewell : async ( context, value ) => {
            await context.commit('getTotalPhysicianCarewell',value);
        },
        getTotalProcedureCarewell : async ( context, value ) => {
            await context.commit('getTotalProcedureCarewell',value);
        },
        getSelectedMemberAvailment : async ( context, value) => {
            await context.commit('getSelectedMemberAvailment',value);
        },
        physicianInformation : async ( context, value ) => {
            await context.commit('physicianInformation',value);
        },
        viewingAvailment : async ( context ) => {
            let value = window.location.pathname === '/availment-center/view'
            await context.commit('viewingAvailment', value);
        },
        procedureInformation : async ( context, value ) => {
            await context.commit('procedureInformation',value);
        },
        amount_covered : async (context, value) => {
            await context.commit('amount_covered', value);
        },
        remaining_balance : async (context, value) => {
            await context.commit('remaining_balance', value);
        },
        getMemberAvailmentByBenefit : async (context, value) => {
            await context.commit('getMemberAvailmentByBenefit', value);
        }
    }
})