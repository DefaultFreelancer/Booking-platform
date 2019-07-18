<template>
    <div class="row">
        <form>
            <div class="input-field col s12">
                <input id="password" v-model="password" type="password" class="validate"
                       :class="{'margin-fix': isSubmitted && !validation.password}" required>
                <label for="password">{{ trans('lang.new_password') }}</label>
                <span v-show="isSubmitted && !validation.password" class="red-text">{{ trans('lang.required_input_field') }}</span>
            </div>
            <div class="input-field col s12">
                <input id="password-confirm" v-model="password_confirmation" type="password" class="validate"
                       :class="{'margin-fix': isSubmitted && !validation.password_confirmation}" required>
                <label for="password-confirm">{{ trans('lang.confirm_password') }}</label>
                <span v-if="isSubmitted && !validation.password_confirmation" class="red-text">{{ trans('lang.required_input_field') }}</span>
                <span v-else-if="not_match && (password_confirmation !== password)" class="red-text">{{ trans('lang.password_not_match') }}</span>
            </div>
            <div class="input-field col s12">
                <button class="btn waves-effect waves-light bluish-back mob-btn" @click.prevent="changePassword" type="submit">{{ trans('lang.save') }} </button>
            </div>
        </form>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        props: ["user"],
        data() {
            return {
                password: '',
                password_confirmation: '',
                not_match: false,
                isSubmitted: false,

            }
        },
        computed: {
            validation: function () {
                return {
                    password: !!this.password.trim(),
                    password_confirmation: !!this.password_confirmation.trim(),
                };
            },
            isValid: function () {
                var validation = this.validation
                return Object.keys(validation).every(function (key) {
                    return validation[key]

                })
            },
        },
        methods: {

            changePassword() {

                let instance = this;

                this.isSubmitted = true;
                if (this.isValid) {

                    if (this.password == this.password_confirmation) {
                        instance.axiosPost('/updatePassword/' + this.user,{
                                password: this.password,
                                password_confirmation: this.password_confirmation
                            },
                            function(response){
                                instance.redirect('/logout');
                                M.toast({html:instance.trans('lang.password_updated_successfully')});
                            },
                            function (error) {
                                this.errors = [];
                                M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                                if (error.response && error.response.data && error.response.data.errors && error.response.data.errors.password) {
                                    this.errors.push(error.response.data.errors.password[0]);
                                }

                            });
                    }
                    else {
                        this.not_match = true;
                    }
                }
            }
        }
    }
</script>