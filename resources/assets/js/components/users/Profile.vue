<template>
    <div>
        <div class="navbar-text hide-on-med-and-down" v-if="profile.is_admin == 1 || profile.role_id > 0 ">
            {{ trans('lang.profile') }}
        </div>
        <div class="profile-wrapper admin-layout-margin-for-col">
            <div class="row margin-fix">
                <div class="col s12 m4 l4 xl3 col-right-padding">
                    <div class="main-layout-card">
                        <div class="main-layout-card-header-with-button">
                            <div class="main-layout-card-content-wrapper">
                                <div class="main-layout-card-header-contents">
                                    <div class="row margin-fix center-align profile-image-container">
                                        <div v-if="avatar">
                                            <img class="responsive-img profile-image materialboxed" :src="avatar">
                                        </div>
                                        <div v-else>
                                            <img class="responsive-img profile-image materialboxed"
                                                 :src="publicPath+'/uploads/profile/'+profile.avatar" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="main-layout-card-content">
                            <div class="center-align user-name-text">
                                <span>{{ profile.first_name+' '+profile.last_name }} </span>
                            </div>
                        </div>
                    </div>

                    <div class="main-layout-card">
                        <div class="collection max-content-height no-border">
                            <a href="#" class="collection-item no-border" :class="{'active-border': isActive==1}"
                               @click="isActive = 1, newTop= 0, newHeight = 49">
                                <h6>{{ trans('lang.profile') }}</h6>
                            </a>
                            <a href="#" class="collection-item no-border" :class="{'active-border': isActive==2}"
                               @click="isActive = 2, newTop = 49, newHeight = 49">
                                <h6>{{ trans('lang.change_password') }}</h6>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col s12 m8 l8 xl9">
                    <div class="main-layout-card" v-show="isActive==1">
                        <div class="main-layout-card-header-with-button">
                            <div class="main-layout-card-content-wrapper">
                                <div class="main-layout-card-header-contents">
                                    <div class="col s12">
                                        <h5 class="bluish-text no-margin">{{ trans('lang.profile_title') }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="main-layout-card-content">
                            <div class="row ">
                                <form enctype="multipart/form-data">
                                    <div class="row margin-fix">
                                        <div class="input-field col s6">
                                            <input id="first_name" v-model="profile.first_name" type="text"
                                                   class="validate" :class="{'invalid': errorInvalid.first_name}"/>
                                            <label for="first_name" :class="{'active': error}">{{
                                                trans('lang.first_name') }}</label>
                                            <span class="red-text helper-text"
                                                  :data-error="trans('lang.required_input_field')"></span>
                                        </div>
                                        <div class="input-field col s6">
                                            <input id="last_name" v-model="profile.last_name" type="text"
                                                   class="validate" :class="{'invalid': errorInvalid.last_name}">
                                            <label for="last_name" :class="{'active': error}">{{ trans('lang.last_name')
                                                }}</label>
                                            <span class="red-text helper-text"
                                                  :data-error="trans('lang.required_input_field')"></span>
                                        </div>
                                        <div class="input-field col s12">
                                            <input id="email" v-model="profile.email" type="email" class="validate"
                                                   :class="{'invalid': errorInvalid.email}">
                                            <label for="email" :class="{'active': error}">{{ trans('lang.email_address')
                                                }}</label>
                                            <span class="red-text helper-text" :data-error="invalidEmailMessage"></span>
                                        </div>
                                        <div class="input-field col s6">
                                            <input id="birth-date" v-model="profile.date_of_birth" type="text"
                                                   class="datepicker">
                                            <label for="birth-date">{{ trans('lang.date_of_birth') }}</label>
                                        </div>
                                        <div class="input-field col s6">
                                            <select v-model="profile.gender" id="gender">
                                                <option value="" disabled>{{ trans('lang.choose_one') }}</option>
                                                <option value="Male"> {{ trans('lang.male') }}</option>
                                                <option value="Female"> {{ trans('lang.female') }}</option>
                                                <option value="Others"> {{ trans('lang.others') }}</option>
                                            </select>
                                            <label>{{ trans('lang.gender') }}</label>
                                        </div>
                                        <div class="input-field col s12">
                                            <div class="file-field input-field">
                                                <div class="btn white bluish-text col s12 m6 l6">
                                                    <span>{{ trans('lang.change_profile_image') }}</span>
                                                    <input type="file" name="avatar" @change="onImageChange"
                                                           accept="image/*">
                                                </div>
                                                <div class="file-path-wrapper">
                                                    <input class="file-path validate" type="text"
                                                           :placeholder="trans('lang.image_only')">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="input-field col s12">
                                            <button class="btn waves-effect waves-light bluish-back mob-btn"
                                                    @click.prevent="setErrorInvalid(),updateProfile()" type="submit">{{
                                                trans('lang.save') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="main-layout-card" v-show="isActive==2">
                        <div class="main-layout-card-header-with-button">
                            <div class="main-layout-card-content-wrapper">
                                <div class="main-layout-card-header-contents">
                                    <div class="col s12">
                                        <h5 class="bluish-text no-margin">{{ trans('lang.change_password') }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="main-layout-card-content">
                            <change-password></change-password>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';

    export default {
        extends: axiosGetPost,
        props: ["profile"],
        data() {
            return {
                first_name: '',
                last_name: '',
                email: '',
                date_of_birth: null,
                gender: '',
                password: '',
                avatar: '',
                errors: [],
                isActive: 1,
                setNone: '',
                error: '',
                errorInvalid: {
                    first_name: false,
                    last_name: false,
                    email: false,
                },
                invalidEmailMessage: '',
            }
        },

        mounted() {
            $('.materialboxed').materialbox();
            var instance = this;
            $('.datepicker').datepicker({
                selectMonths: true, // Creates a dropdown to control month
                selectYears: 70, // Creates a dropdown of 70 years to control year,
                today: 'Today',
                clear: 'Clear',
                close: 'Ok',
                format: this.dateFormat,
                closeOnSelect: true, // Close upon selecting a date,
                max: [moment().format("YYYY"), moment().format("MM") - 1, moment().format("DD")]
            });
            $('select').formSelect();
            $('.datepicker').on('change', function () {
                var dob = $(this).val();
                instance.profile.date_of_birth = dob;
            });
            $('#gender').on('change', function () {
                var gender2 = $(this).val();
                instance.profile.gender = gender2
            });

            $(document).ready(function () {
                M.updateTextFields();
            });
            $('#first_name').on('input', function () {
                instance.errorInvalid.first_name = false;
            });
            $('#last_name').on('input', function () {
                instance.errorInvalid.last_name = false;
            });
            $('#email').on('input', function () {
                instance.errorInvalid.email = false;
                instance.invalidEmailMessage = instance.trans('lang.invalid_email');
            });
        },
        methods: {
            setErrorInvalid() {
                this.error = false;
                this.errorInvalid.first_name = false
                this.errorInvalid.last_name = false;
                this.errorInvalid.email = false;
            },
            onImageChange(event) {
                // Reference to the DOM input element
                var input = event.target;
                // Ensure that you have a file before attempting to read it
                if (input.files && input.files[0]) {
                    // create a new FileReader to read this image and convert to base64 format
                    var reader = new FileReader();
                    // Define a callback function to run, when FileReader finishes its job
                    reader.onload = (e) => {
                        // Note: arrow function used here, so that "this.imageData" refers to the imageData of Vue component
                        // Read image as base64 and set to imageData
                        this.profile.avatar = e.target.result;
                        this.avatar = e.target.result;
                    }
                    // Start the reader job - read file as a data url (base64 format)
                    reader.readAsDataURL(input.files[0]);
                }
                else {
                    this.profile.avatar = '';
                }
            },

            updateProfile() {
                let instance = this;
                if (this.profile.first_name == '') {
                    this.$nextTick(function () {
                        instance.error = true;
                        instance.errorInvalid.first_name = true;
                    })

                }
                if (this.profile.last_name == '') {
                    this.$nextTick(function () {
                        instance.error = true;
                        instance.errorInvalid.last_name = true;
                    })
                }

                var emailFormat = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if (!this.profile.email.match(emailFormat)) {
                    if (this.profile.email == '') {
                        instance.invalidEmailMessage = instance.trans('lang.required_input_field');
                    }
                    else {
                        instance.invalidEmailMessage = instance.trans('lang.invalid_email');
                    }
                    this.$nextTick(function () {
                        instance.error = true;
                        instance.errorInvalid.email = true;
                    })
                }
                if (this.profile.first_name != '' && this.profile.last_name != '' && this.profile.email.match(emailFormat)) {

                    if (this.profile.date_of_birth != null) {

                        this.profile.date_of_birth = moment(this.profile.date_of_birth, instance.dateFormat.toUpperCase()).format('YYYY-MM-DD');
                    }

                    this.error = true;
                    instance.axiosPost('/profile/' + this.profile.id, {
                            first_name: instance.profile.first_name,
                            last_name: instance.profile.last_name,
                            email: instance.profile.email,
                            date_of_birth: instance.profile.date_of_birth,
                            gender: instance.profile.gender,
                            password: instance.profile.password,
                            avatar: instance.profile.avatar,
                        },
                        function (response) {
                            window.location.reload();
                            M.toast({html: instance.trans('lang.profile_update_success')});
                        },
                        function (error) {
                            instance.errors = [];
                            M.toast({html: instance.trans('lang.getting_problems'), classes: 'red lighten-1'});
                            if (error.response && error.response.data && error.response.data.errors && error.response.data.errors.first_name) {
                                this.errors.push(error.response.data.errors.first_name[0]);
                            }
                        }
                    );
                }
            }
        }
    }

</script>