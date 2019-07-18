<template>
    <div class="main-layout-card">
        <div class="main-layout-card-header">
            <div class="main-layout-card-content-wrapper">
                <div class="main-layout-card-header-contents">
                    <h5 class="bluish-text no-margin">{{ trans('lang.application_settings') }}</h5>
                </div>
            </div>
        </div>
        <div class="main-layout-card-content">
            <div class="modal-loader-parent" v-if="hidePreloader!='hide'">
                <div class="modal-loader-child">
                    <preloaders :loderType="preloaderType"></preloaders>
                </div>
            </div>
            <div v-show="hidePreloader=='hide'">
                <form>
                    <div class="row">
                        <h6 class=" col s12">{{ trans('lang.general_settings') }}</h6>
                        <div class="row margin-fix">
                            <div class="input-field col s12 m6 l3">
                                <input id="appname" v-model="item.app_name" type="text"
                                       class="validate">
                                <label for="appname">{{ trans('lang.app_name') }}</label>
                            </div>
                            <div class="input-field col s12 m6 l7 offset-l1">
                                <div class="file-field">
                                    <div class="btn white bluish-text col s12 m6 l6">
                                        <span>{{ trans('lang.change_application_logo') }}</span>
                                        <input id="app-logo" type="file" name="app_logo" @change="onImageChange"
                                               accept="image/*">
                                    </div>
                                    <div class="file-path-wrapper">
                                        <input class="file-path" type="text" :placeholder="trans('lang.image_only')"
                                               v-model="app_logo">
                                    </div>
                                </div>
                            </div>
                            <div class="input-field col s12 m6 l3">
                                <!--<span class="caret">▼</span>-->
                                <select id="max_row_per_table" v-model="item.max_row_per_table">
                                    <option value="" disabled selected>{{ trans('lang.choose_one') }}</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="500">500</option>
                                </select>
                                <label>{{ trans('lang.rows_per_table') }}</label>
                            </div>
                            <div class=" col s12 m6 l7 offset-l1">
                                <div class="file-field input-field">
                                    <div class="btn white bluish-text col s12 m6 l6">
                                        <span>{{ trans('lang.change_background_image') }}</span>
                                        <input type="file" name="background_image" @change="backgroundImageChange"
                                               accept="image/*">
                                    </div>
                                    <div class="file-path-wrapper">
                                        <input id="background-image" class="file-path" type="text"
                                               :placeholder="trans('lang.image_only')" v-model="backgroundImageName">
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <h6 class="col s12">{{ trans('lang.date_time_options') }}</h6>
                        <div class="col s12 m6 l3">
                            <div class="input-field">
                                <div class="label-for-radio">
                                    {{ trans('lang.time_format') }}
                                </div>
                            </div>
                            <label for="time-format-1">
                                <input class="with-gap" v-model="item.time_format" type="radio" name="time-format"
                                       id="time-format-1" value="24h">
                                <span>{{ trans('lang.24h') }}</span></label>
                            <label for="time-format-2">
                                <input class="with-gap" v-model="item.time_format" type="radio" name="time-format"
                                       id="time-format-2" value="12h">
                                <span>{{ trans('lang.12h') }}</span></label>
                        </div>
                        <div class="input-field col s12 m6 l3 offset-l1">
                            <select id="dateformat" name="date-format" v-model="item.date_format">
                                <option value="" disabled>{{ trans('lang.choose_one') }}</option>
                                <option value="d/m/Y">{{ trans('lang.dd/mm/yyyy') }}</option>
                                <option value="m/d/Y">{{ trans('lang.mm/dd/yyyy') }}</option>
                                <option value="Y/m/d">{{ trans('lang.yyyy/mm/dd') }}</option>
                                <option value="d-m-Y">{{ trans('lang.dd-mm-yyyy') }}</option>
                                <option value="m-d-Y">{{ trans('lang.mm-dd-yyyy') }}</option>
                                <option value="Y-m-d">{{ trans('lang.yyyy-mm-dd') }}</option>
                                <option value="d.m.Y">{{ trans('lang.dd_mm_yyyy') }}</option>
                                <option value="m.d.Y">{{ trans('lang.mm_dd_yyyy') }}</option>
                                <option value="Y.m.d">{{ trans('lang.yyyy_mm_dd') }}</option>
                            </select>
                            <label>{{ trans('lang.date-format') }}</label>
                        </div>
                    </div>
                    <div class="row">
                        <h6 class=" col s12">{{ trans('lang.currency_settings') }}</h6>
                        <div class="row magin-fix">
                            <div class="input-field col s12 m6 l3">
                                <input id="currency-symbol" v-model="item.currency_symbol" type="text"
                                       class="validate" value="$">
                                <label for="currency-symbol">{{ trans('lang.currency_symbol') }}</label>
                            </div>
                            <div class="input-field col s12 m6 l3  offset-l1">
                                <input id="currency-code" v-model="item.currency_code" type="text"
                                       class="validate">
                                <label for="currency-code"><span>{{ trans('lang.currency_code') }}</span><span> <i class="la la-info-circle setting-info-icon tooltipped" data-position="top" :data-tooltip="trans('lang.please_use_valid_currency_code')"></i></span></label>
                            </div>
                            <div class="input-field col s12 m6 l3 offset-l1">
                                <select id="currencyformat" name="currency-format" v-model="item.currency_format">
                                    <option value="" disabled selected>{{ trans('lang.choose_one') }}</option>
                                    <option value="left">$0.00</option>
                                    <option value="right">0.00$</option>
                                    <option value="left-space">$ 0.00</option>
                                    <option value="right-space">0.00 $</option>
                                </select>
                                <label>{{ trans('lang.currency_position') }}</label>
                            </div>
                        </div>
                        <div class="row margin-fix">
                            <div class="input-field col s12 m6 l3">
                                <select id="numberdeciaml" name="number_of_decimal" v-model="item.number_of_decimal">
                                    <option value="0">0</option>
                                    <option value="2">2</option>
                                </select>
                                <label>{{ trans('lang.number_of_decimal') }}</label>
                            </div>
                            <div class="input-field col s12 m6 l3 offset-l1">
                                <select id="decimalseparator" name="thousand-separator"
                                        v-model="item.decimal_separator">
                                    <option value="" disabled selected>{{ trans('lang.choose_one') }}</option>
                                    <option value=".">100.11</option>
                                    <option value=",">100,11</option>
                                </select>
                                <label>{{ trans('lang.decimal_separator') }}</label>
                            </div>
                            <div class="input-field col s12 m6 l3 offset-l1">
                                <select id="thousandseparator" name="thousand-separator"
                                        v-model="item.thousand_separator">
                                    <option value="" disabled selected>{{ trans('lang.choose_one') }}</option>
                                    <option value="space">1 00 000</option>
                                    <option value=",">1,00,000</option>
                                    <option value=".">1.00.000</option>
                                </select>
                                <label>{{ trans('lang.thousand_separator') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row margin-fix">
                        <h6 class="col s12">{{ trans('lang.invoice_settings') }}</h6>
                        <div class="input-field col s12 m6 l3">
                            <input id="company_name" v-model="item.company_name" type="text"
                                   class="validate">
                            <label for="company_name">{{ trans('lang.company_name') }}</label>
                        </div>
                        <div class="input-field col s12 m6 l7 offset-l1">
                            <div class="file-field">
                                <div class="btn white bluish-text col s12 m6 l6">
                                    <span>{{ trans('lang.change_invoice_logo') }}</span>
                                    <input id="invoice-logo" type="file" name="invoice-logo" @change="onInvoiceChange"
                                           accept="image/jpg">
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path" type="text" :placeholder="trans('lang.choose_jpg_image_only')"
                                           v-model="invoice_logo">
                                </div>
                            </div>
                        </div>
                        <div class="col s12 landing_page">
                            <div class="input-field col s12 col s12 m12 l11">
                                <textarea id="company_info" v-model="item.company_info"
                                          class="landing_page_text landing_page materialize-textarea"></textarea>
                                <label for="company_info">{{ trans('lang.company_info') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row margin-fix">
                        <h6 class="col s12">{{ trans('lang.language_settings') }}</h6>
                        <div class="input-field col s12 m6 l3">
                            <select id="language" name="preferred-language" v-model="item.language_setting">
                                <option value="" disabled selected>{{ trans('lang.choose_one') }}</option>
                                <option v-for="language in langdir" :value="language">{{
                                    language.charAt(0).toUpperCase() + language.slice(1) }}
                                </option>
                            </select>
                            <label>{{ trans('lang.preferred_language') }}</label>
                        </div>
                        <div class="input-field col s12 m6 l7 offset-l1">
                            <button class="btn white waves-effect waves-light bluish-text mob-btn"
                                    @click.prevent="languageCacheClear()" type="submit">
                                {{ trans('lang.clear_language_cache') }}
                            </button>
                        </div>
                    </div>
                    <div class="row margin-fix">
                        <h6 class="col s12">{{ trans('lang.landing_page_settings') }}</h6>
                        <div class="col s12 landing_page">
                            <div class="input-field col s12 m12 l11">
                                <input id="landing_page_header" v-model="item.landing_page_header" type="text"/>
                                <label for="landing_page_header">{{ trans('lang.landing_page_header') }}</label>
                            </div>
                        </div>
                        <div class="col s12 landing_page">
                            <div class="input-field col  s12 m12 l11">
                                <textarea id="landing_page_message" v-model="item.landing_page_message"
                                          class="landing_page_text landing_page materialize-textarea"></textarea>
                                <label for="landing_page_message">{{ trans('lang.landing_page_message') }}</label>
                            </div>
                        </div>
                        <div class="col s12">
                            <button class="btn waves-effect waves-light bluish-back mob-btn"
                                    @click.prevent="is_disable(),update()" :disabled="is_disabled" type="submit">{{
                                trans('lang.save') }}
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';

    export default {
        extends: axiosGetPost,
        data() {
            return {
                item: {
                    time_format: '',
                    date_format: '',
                    time_zone: '',
                    currency_symbol: '',
                    currency_format: '',
                    thousand_separator: '',
                    language_setting: '',
                    choose_currency: '',
                    decimal_separator: '',
                    number_of_decimal: '',
                    max_row_per_table: '',
                    app_name: '',
                    app_logo: '',
                    invoice_logo:'',
                    company_name:'',
                    company_info:'',
                    landing_page_message: '',
                    landing_page_header: '',
                },
                app_logo: '',
                invoice_logo:'',
                backgroundImage: '',
                backgroundImageName: '',
                is_disabled: false,
                tzlist: [],
                langdir: [],
                hidePreloader: '',
                preloaderType: 'load',
            }
        },
        mounted() {
            this.readLanDir();
            this.readData();
            $('select').formSelect();
            $('.tooltipped').tooltip();
            let instance = this;
            $('.datepicker').datepicker({
                selectMonths: true, // Creates a dropdown to control month
                selectYears: 70, // Creates a dropdown of 70 years to control year,
                today: 'Today',
                clear: 'Clear',
                close: 'Ok',
                format: 'yyyy-mm-dd',
                closeOnSelect: false // Close upon selecting a date,
            });
            $('.datepicker').on('change', function () {
                var dob = $(this).val();
                // instance.profile.date_of_birth = dob;
            });
        },
        methods: {

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
                        this.item.app_logo = e.target.result;
                    }
                    this.app_logo = input.files[0].name;
                    // Start the reader job - read file as a data url (base64 format)
                    reader.readAsDataURL(input.files[0]);
                }
                else {
                    this.item.app_logo = '';
                }
            },
            onInvoiceChange(event) {
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
                        this.item.invoice_logo = e.target.result;
                    }
                    this.invoice_logo = input.files[0].name;
                    // Start the reader job - read file as a data url (base64 format)
                    reader.readAsDataURL(input.files[0]);
                }
                else {
                    this.item.invoice_logo = '';
                }
            },
            backgroundImageChange(event) {
                var input = event.target;
                if (input.files && input.files[0]) {

                    var reader = new FileReader();
                    reader.onload = (e) => {
                        this.backgroundImage = e.target.result;
                    }
                    this.backgroundImageName = input.files[0].name;
                    reader.readAsDataURL(input.files[0]);
                }
                else {
                    this.backgroundImage = '';
                }
            },
            setPreloader: function (type, action) {
                //this.$emit('setPreloader', e,f);
                this.preloaderType = type;
                this.hidePreloader = action;

            },
            languageCacheClear() {
                let instance = this;
                instance.setPreloader('load', '');
                this.axiosGet('/clear-cache',
                    function (response) {
                        M.toast({html: instance.trans('lang.the_language_cache_has_been_removed')})
                        instance.setPreloader('load', 'hide');
                        instance.is_disabled = false;
                        instance.$nextTick(function () {
                            M.updateTextFields();
                        });
                    },
                    function (response) {
                    },
                );
            },
            readLanDir() {
                let instance = this;
                this.axiosGet('/getlangdir',
                    function (response) {
                        instance.langdir = response.data;
                        setTimeout(function () {
                            $('#language').on('change', function () {
                                let value = $(this).val();
                                instance.item.language_setting = value;
                            });
                        });
                    },
                    function (error) {
                    },
                );
            },
            readData() {
                let instance = this;
                instance.setPreloader('load', '');
                this.axiosGet('/basicsettingdata',
                    function (response) {
                        instance.item = response.data;
                        setTimeout(function () {
                            instance.setPreloader('load', 'hide');
                            $('#currency').on('change', function () {
                                let value = $(this).val();
                                instance.item.choose_currency = value;
                            });
                            $('#dateformat').on('change', function () {
                                let value = $(this).val();
                                instance.item.date_format = value;
                            });
                            $('#timezone').on('change', function () {
                                let value = $(this).val();
                                instance.item.time_zone = value;
                            });
                            $('#currencyformat').on('change', function () {
                                let value = $(this).val();
                                instance.item.currency_format = value;
                            });
                            $('#thousandseparator').on('change', function () {
                                let value = $(this).val();
                                instance.item.thousand_separator = value;
                                if (value == ",") {
                                    instance.item.decimal_separator = ".";
                                }
                                if (value == ".") {
                                    instance.item.decimal_separator = ",";
                                }
                                instance.$nextTick(function () {
                                    $('#decimalseparator').formSelect();
                                })
                            });
                            $('#decimalseparator').on('change', function () {
                                let value = $(this).val();
                                instance.item.decimal_separator = value;
                                if (value == "," && instance.item.thousand_separator == ",") {
                                    instance.item.thousand_separator = ".";
                                }
                                if (value == "." && instance.item.thousand_separator == ".") {
                                    instance.item.thousand_separator = ",";
                                }

                                instance.$nextTick(function () {
                                    $('#thousandseparator').formSelect();
                                })
                            });
                            $('#numberdeciaml').on('change', function () {
                                let value = $(this).val();
                                instance.item.number_of_decimal = value;
                            });
                            $('#max_row_per_table').on('change', function () {
                                let value = $(this).val();
                                instance.item.max_row_per_table = value;
                            });
                            $('#landing_page_message').on('change', function () {
                                let value = $(this).val();
                                instance.item.landing_page_message = value;
                            });
                            $('#landing_page_header').on('change', function () {
                                let value = $(this).val();
                                instance.item.landing_page_header = value;
                            });
                            M.updateTextFields();
                            $('select').formSelect();
                        });

                    },
                    function (error) {
                        instance.setPreloader('load', 'hide');
                    },
                );
            },
            sepChange() {
                let instance = this;
                if (instance.item.decimal_separator == ",") {
                    instance.item.thousand_separator = ".";
                }
                if (instance.item.decimal_separator == ".") {
                    instance.item.thousand_separator = ",";
                }
            },
            update() {
                let instance = this;
                instance.setPreloader('load', '');
                instance.axiosPost('/basic-setting', {
                        choose_currency: this.item.choose_currency,
                        currency_code: this.item.currency_code,
                        time_format: this.item.time_format,
                        date_format: this.item.date_format,
                        time_zone: this.item.time_zone,
                        currency_symbol: this.item.currency_symbol,
                        currency_format: this.item.currency_format,
                        thousand_separator: this.item.thousand_separator,
                        decimal_separator: this.item.decimal_separator,
                        number_of_decimal: this.item.number_of_decimal,
                        language_setting: this.item.language_setting,
                        max_row_per_table: this.item.max_row_per_table,
                        app_name: this.item.app_name,
                        app_logo: this.item.app_logo,
                        invoice_logo : this.item.invoice_logo,
                        company_name : this.item.company_name,
                        company_info : this.item.company_info,
                        background_image: this.backgroundImage,
                        landing_page_message: this.item.landing_page_message,
                        landing_page_header: this.item.landing_page_header,
                    },
                    function (response) {
                        window.location.reload();
                        M.toast({html: instance.trans('lang.apps_successfully_update')});
                        instance.setPreloader('load', 'hide');
                        instance.is_disabled = false;
                        instance.app_logo = '';
                        instance.backgroundImageName = '';
                    },
                    function (error) {
                        instance.errors = error.data.errors
                        M.toast(instance.trans('lang.getting_problems'), 6000, 'red lighten-1');
                        instance.setPreloader('load', 'hide');
                        instance.is_disabled = false;
                    }
                );
            },
            is_disable() {
                this.is_disabled = true;
            }
        }
    }
</script>