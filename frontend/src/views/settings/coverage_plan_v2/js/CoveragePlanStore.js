import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const defaultState = () => {
    return {
        SELECTED_PROCEDURES:[],
        MAX_LIMIT_PER_ILLNESS_OR_YEAR: 0,
        BENEFIT_WITH_PROCEDURES:[],
        SET_BENEFIT_PROCEDURES:[]
    }
}

export const coveragePlanData = new Vuex.Store({
    // state:{
    //     SELECTED_PROCEDURES:[],
    //     MAX_LIMIT_PER_ILLNESS_OR_YEAR: 0,
    //     BENEFIT_WITH_PROCEDURES:[],
    //     SET_BENEFIT_PROCEDURES:[]
    // },
    state : defaultState(),
    getters:{
        SELECTED_PROCEDURES: state => {
            return state.SELECTED_PROCEDURES;
        },
        MAX_LIMIT_PER_ILLNESS_OR_YEAR: state => {
            return state.MAX_LIMIT_PER_ILLNESS_OR_YEAR;
        },
        BENEFIT_WITH_PROCEDURES : state => {
            return state.BENEFIT_WITH_PROCEDURES;
        },
        SET_BENEFIT_PROCEDURES : state => {
            return state.SET_BENEFIT_PROCEDURES;
        }
    },
    mutations:{
        getSelectedProcedures : (state, selected) => {
            //selected.index = benefit type index
            //selected.procedures = procedure selected per procedure types

            // NOTE:
            // to create individual copy of object
            // I use JSON.parse( JSON.stringify( selected.procedures ) )
            // JSON.parse( JSON.stringify( selected.procedures ) ) solve the problem of changing procedure amount
            state.SELECTED_PROCEDURES[selected.index] = {
                benefits : selected.form,
                procedures : JSON.parse( JSON.stringify( selected.procedures ) )
            }
        },
        getSelectedCount: (state, selected) => {
            let item_count = 0;
            let data = selected.procedures;
            data.forEach( (arrData,key) => {
                if(arrData != null)
                {
                    item_count += arrData.length;
                }
            });
            // let count = data.reduce((acc,item) => acc += item.length, 0)
            state.BENEFIT_WITH_PROCEDURES[selected.index].item_count = item_count;
        },
        getMaxLimitPerIllnessOrYear: (state, number) => {
            state.MAX_LIMIT_PER_ILLNESS_OR_YEAR = number
        },
        getBenefitWithProcedures : (state, value) => {
            // NOTE:
            // to create individual copy of object
            // I use JSON.parse( JSON.stringify( selected.procedures ) )
            // JSON.parse( JSON.stringify( selected.procedures ) ) solve the problem of changing procedure amount
            state.BENEFIT_WITH_PROCEDURES = JSON.parse( JSON.stringify( value ) );
        },
        setProcedures : (state,value) => {
            state.SET_BENEFIT_PROCEDURES = value.coverage_plan_benefits;
        },
        clearCoveragePlanDataVuex : (state) => {
            // state.SELECTED_PROCEDURES = [];
            // state.MAX_LIMIT_PER_ILLNESS_OR_YEAR =  0;
            // state.BENEFIT_WITH_PROCEDURES = [];
            // state.SET_BENEFIT_PROCEDURES = [];
            Object.assign(state, defaultState());
        }
    },
    actions:{
        getSelectedAndCount: (context,selected) => {
            context.commit('getSelectedProcedures',selected);
            context.commit('getSelectedCount',selected);
        },
        getBenefitWithProcedures : (context,value) => {
            context.commit('getBenefitWithProcedures',value);
        },
        setProcedures : (context,value) => {
            context.commit('setProcedures',value)
        },
        clearCoveragePlanDataVuex : (context) => {
            context.commit('clearCoveragePlanDataVuex');
        }
    },
});