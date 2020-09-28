<template>
    <v-container fluid grid-list-xl>
        <v-flex xs12 class="text-xl-center text-xs-center text-md-center text-lg-center">
          <v-icon size="128" color="orange">mdi-alert</v-icon>
          <div class="display-1 py-2 text-orange" color="orange">Access Restricted!</div>
          <div class="title py-1">You don't have access to this page.</div>
          <div class="subheading">Please contact your Administrator</div>
          <div class="pt-3">
              <v-btn large color="primary"><v-icon class="mr-2">mdi-arrow-left</v-icon>   Go back</v-btn>
          </div>
      </v-flex>
    </v-container>
</template>

<script>
    export default {
        data: () => ({
        })
    }
</script>

<style lang="scss">
    @import "../../scss/app.scss";

    .v-breadcrumbs li a {
        font-size: 16px;
    }

    .doctor-center__menu {
        display: grid;
        grid-template-columns: repeat(2, 1fr);

        .v-text-field {
            width: 60%;
            align-self: center;
        }

        .v-btn {
            height: 40px;
        }

        .v-dialog__container
        {
            justify-self: end;
        }
        .v-text-field {
        font-size: 15px;

        .v-input__slot {
          border: 1px solid #ced4da;
        }
      }
    }

</style>