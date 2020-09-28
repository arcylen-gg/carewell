<template>
<div class="bottom-holder">
    <div class="title text-dark font-weight-bold mb-2">Procedures</div>
    <v-divider></v-divider>
    <span class="top-holder">
        <span></span>
        <div>
            <v-text-field
                v-model="search"
                append-icon="search"
                label="Search"
                single-line
                hide-details
            ></v-text-field>
        </div>
    </span>
    <v-tabs dark slot="extension" color="primary" show-arrows fixed-tabs>
        <v-tabs-slider color="#DF7E00"></v-tabs-slider>
        <v-tab v-for="type in procedure_types" :key="type.name">
            {{ type.name }}
        </v-tab>
        <v-tab-item v-for="(type, index) in procedure_types" :key="type.name">
            <v-card flat>
                <v-data-table select-all hide-actions :headers="headers" :items="type.procedures" v-model="selected[index]" :search="search" :custom-filter="customFilter" class="text-xs-center" style="border: 1px solid #E0E0E0;">
                    <template slot="items" slot-scope="props" v-if="props.item.is_archived == 0">
                        <td>
                            <!-- <input type="text" v-model=""> -->
                            <v-checkbox v-model="props.selected" primary hide-details></v-checkbox>
                        </td>
                        <td class="text-xs-center">{{ props.item.name }}</td>
                        <td class="text-xs-center">
                            <v-text-field v-model="props.item.amount"></v-text-field>
                        </td>
                    </template>
                </v-data-table>
                <div class="button-holder mt-3">
                    <create-plan-description :form="{procedure_type_id: type.id}"><v-btn color="info" class="m-0">ADD PROCEDURE</v-btn></create-plan-description>
                    
                    <v-spacer></v-spacer>
                    <!-- <v-select color="accent" :items="all_list" label="Select Type" solo flat></v-select> -->
                </div>
            </v-card>
        </v-tab-item>
    </v-tabs>
</div>
</template>