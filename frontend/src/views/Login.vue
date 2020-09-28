<template>
    <v-app id="login" class="primary">
        <v-content>
            <v-container fluid fill-height>
                <v-layout align-center justify-center>
                    <v-flex xs12 sm8 md4 lg4>
                        <v-form ref="login_form" @submit.prevent="loginAuthentication">
                            <v-card class="elevation-1 pa-3">
                                <v-card-text>
                                    <div class="layout column align-center">
                                        <div class="img-holder">
                                            <img src="../assets/carewell-logo.jpg">
                                        </div>
                                        <h1 class="flex my-4 primary--text">Welcome to Carewell</h1>
                                    </div>
                                    <v-alert :value="true" type="error" color="red lighten-1" v-if="error_message">{{ error_message }}</v-alert>
                                        <v-text-field :disabled="loading" append-icon="person" name="login" label="Login" type="email" v-model="form.username"></v-text-field>
                                        <v-text-field :disabled="loading" append-icon="lock" name="password" label="Password" id="password"
                                            type="password" v-model="form.password"></v-text-field>
                                </v-card-text>
                                <v-card-actions>
                                    <input type="submit" ref="login_submit" style="display: none;">
                                    <v-spacer></v-spacer>
                                    <v-btn block color="accent" @click="login" :loading="loading">Login</v-btn>
                                </v-card-actions>
                            </v-card>
                            
                        </v-form>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
    export default {
        data: () => ({
            loading: false,
            form: {
                grant_type: "password",
                client_id: 1,
                client_secret: "ZWEWo9Es5jJpmW74pNAdVeoTKJWRKNLzwZovlmRv",
                scope: "",
                username: null,
                password: null
            },
            error_message : null
        }),

        methods: 
        {
            login() 
            {
                this.$refs.login_submit.click()
            },
            loginAuthentication()
            {
                if(this.$refs.login_form.validate())
                {
                    this.loading = true
                    this.error_message = null

                    axios.post('/oauth/token', this.form).then(res =>
                    {
                        this.$store.commit('login', res.data);
                        axios.defaults.headers.common['Authorization'] = "Bearer "+ res.data.access_token;
                        this.$store.commit('store_user', res.data);
                        this.loadDependencies()
                    })
                    .catch(err =>
                    {
                        this.error_message  = err.response.data.message;
                    })
                    .finally(()=>{
                        this.loading = false
                    });
                }
                else
                {
                    this.loading        = false;
                }
            },
            loadDependencies()
            {
                this.get_user()
                this.get_pages()
                this.get_benefits()
                this.get_procedure_types()
                this.get_count()
                this.$router.push('/dashboard/home')
            }

        },
        mounted () 
        {   
            
        }
    };


</script>
<style scoped lang="css">
    #login {
        height: 50%;
        width: 100%;
        position: absolute;
        top: 0;
        left: 0;
        content: "";
        z-index: 0;
    }
    .img-holder
    {
        width: 100% !important;
    }
    img
    {
        width: 100% !important;
    }
</style>