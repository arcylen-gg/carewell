<template>
    <span>
        <v-dialog v-model="dialog" persistent scrollable max-width="500px">
            <v-btn style="width: 100%; height: 35px;" slot="activator" color="accent" depressed @click="loadDialog">SORT COMPANY</v-btn>
            <v-card>
                <v-card-title class="title" style="background-color: #222C3C; color: #fff;"><strong>SORT BY COMPANY</strong></v-card-title>
                <v-card-text>
                    <v-data-table
                        v-model="selected"
                        :headers="headers"
                        :items="company"
                        :pagination.sync="sorting"
                        hide-actions
                        select-all
                        class="elevation-1"
                    >
                    <template slot="headers" slot-scope="props">
                        <tr>
                        <th>
                            <v-checkbox
                            :input-value="props.all"
                            :indeterminate="props.indeterminate"
                            primary
                            hide-details
                            @click.stop="toggleAll"
                            ></v-checkbox>
                        </th>
                        <th
                            v-for="header in props.headers"
                            :key="header.text"
                            class="text-xs-center"
                        >
                            {{ header.text }}
                        </th>
                        </tr>
                    </template>
                    <template slot="items" slot-scope="props">
                        <tr :active="props.selected" @click="props.selected = !props.selected">
                        <td class="text-xs-center">
                            <v-checkbox
                            :input-value="props.selected"
                            primary
                            hide-details
                            ></v-checkbox>
                        </td>
                        <td class="text-xs-center">{{ props.item.name }}</td>
                        </tr>
                    </template>
                    </v-data-table>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>   
                    <v-spacer></v-spacer>
                    <v-btn color="tertiary" depressed dark @click="close">CANCEL</v-btn>
                    <v-btn color="accent" depressed type="submit" ref="submit_button" @click="save">SUBMIT</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </span>
</template>

<script>
export default {
    data : () => ({
        dialog: false,
        checkedAll: true,
        requiredRules: [
            v => !!v || "Input is required"
        ],
        company : [],
        headers: [
            {text: 'COMPANY NAME'},
        ],
        selected : [],
        filters : {},
        sorting: {
            rowsPerPage: -1,
        },
    }),
    props : {
        company_list : {
            type : Array,
        }
    },
    methods : {
        toggleAll () {
            if (this.selected.length) this.selected = []
            else this.selected = this.company.slice()
        },
        close() {
            this.dialog = false
        },
        loadDialog(){
            this.company = this.company_list;
            
        },
        save(){
            let arrayData = [];
            this.selected.forEach((arr, ind) => {
                // let arrayID = {
                //     id : arr.id,
                //     name : arr.name
                // };
                arrayData.push(arr.id);
            });
            EventBus.$emit('sortCompany', arrayData)
            this.dialog = false;
        }
    }
}
</script>

<style lang="scss">
    @import "../../../../scss/app.scss";

    .table-procedures {
        width : 100%;

        & td, & th {
            height: 3em;
        };

        &-procedures:hover {
            background-color:#e0e0e0;
        }

        &-procedure-data {
            text-align: center;
            vertical-align: middle;
        };
    }
</style>