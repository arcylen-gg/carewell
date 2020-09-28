<template>
    <span>
        <v-label><h3>Caller Information</h3></v-label>
        <div class="my-0 py-0 holder">
            <v-text-field 
                v-model="form.caller_name" 
                color="accent" 
                label="Full Name" 
                :rules="requiredRules" 
                :disabled="for_viewing"
                outline>
            </v-text-field>  
            <v-text-field
                v-model="form.caller_position" 
                :default="form.caller_position" 
                label="Position"
                color="accent" 
                :disabled="for_viewing" 
                outline
            ></v-text-field>
            <v-text-field 
                v-model="form.caller_contact_number" 
                color="accent" 
                label="Contact Number" 
                :disabled="for_viewing" 
                outline/>
            <v-menu ref="caller_date_menu" 
                :close-on-content-click="false" 
                v-model="caller_date_val"
                :nudge-right="40" 
                lazy transition="scale-transition" 
                offset-y 
                full-width 
                min-width="290px"
                :disabled="for_viewing"
            >
                <date-format
                    slot="activator" 
                    v-model="form.caller_date" 
                    color="accent" 
                    label="Date" 
                    append-icon="event" 
                    :rules="requiredRules" 
                    readonly 
                    :disabled="for_viewing"
                    outline
                ></date-format>
                <v-date-picker 
                    ref="caller_date_picker" 
                    v-model="form.caller_date" 
                    :max="date"
                    min="1950-01-01" 
                    @change="caller_date_save"
                />
            </v-menu>
        </div>
    </span>
</template>
<script>
import { availmentData } from '../../mixins/AvailmentStore.js';
import AvailmentAutoSaveMixins from '../../mixins/AvailmentAutoSaveMixins';

export default {
    mixins : [ AvailmentAutoSaveMixins ],
    data : () => ({
        form: {
            caller_position : null,
            caller_contact_number : null,
        },
        caller_date_menu : [],
        caller_date_val : false,
        // relationship: ['Mother', 'Father', 'Child', 'Spouse', 'Uncle', 'Aunt', 'Brother', 'Sister','Grandfather', 'Grandmother', 'Nephew', 'Niece', 'Cousin'],
        requiredRules: [
            v => !!v || "Input is required"
        ],
        date : new Date().toISOString().substr(0, 10),
        for_viewing : false,
    }),
    methods : {
        caller_date_save(date) {
            this.$refs.caller_date_menu.save(date)
        }
    },
    mounted(){
        availmentData.dispatch('viewingAvailment');
        this.for_viewing = availmentData.state.form.viewingAvailment;
    },
    updated(){
        this.AVAILMENT_AUTOSAVE_MIXINS_AUTOSAVE(this.form,'callerInformation');
    }
}
</script>
