<template>
    <div>
        <div class="row margin-fix modal-layout-header">
            <div class="col s10">
                <h5 class="bluish-text no-margin" v-if="id">{{ trans('lang.edit_holiday') }}</h5>
                <h5 class="bluish-text no-margin" v-else>{{ trans('lang.add_holiday') }}</h5>
            </div>
            <div class="col s2 right-align">
                <a href="#" class="modal-close"  @click.prevent="setActive(),setActive2()"><i class="material-icons dp48 grey-text icon-vertically-middle">clear</i></a>
            </div>
        </div>
        <div class="modal-loader-parent"  v-if="hidePreloader!='hide'">
            <div class="modal-loader-child">
                <preloaders :loderType="preloaderType"></preloaders>
            </div>
        </div>
        <div class="modal-layout-content" v-show="hidePreloader=='hide'">
            <div class="row">
                <div class="input-field col s12">
                    <input id="title" type="text" v-model="title" :class="{'invalid': errorInvalid.title}">
                    <label for="title"  :class="{'active': error}">{{ trans('lang.holiday_title') }}</label>
                    <span class="helper-text" :data-error="trans('lang.required_input_field')"></span>
                </div>
                <div class="input-field col s12">
                    <input id="start-date" type="text"  class="datepicker" v-model="start_date" :class="{'invalid': errorInvalid.start_date}">
                    <label for="start-date" :class="{'active': error}">{{ trans('lang.start_date') }}</label>
                    <span class="helper-text" :data-error="trans('lang.required_input_field')"></span>
                </div>
                <div class="input-field col s12">
                    <input id="end-date" type="text" class="datepicker" v-model="end_date" :class="{'invalid': errorInvalid.end_date}">
                    <label for="end-date" :data-error="dateEndError" :class="{'active': error}">{{ trans('lang.end_date') }}</label>
                    <span class="helper-text" :data-error="dateEndError"></span>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <button class="btn waves-effect waves-light bluish-back mob-btn" type="submit" @click.prevent="is_disable(),setErrorInvalid(),save()" :disabled="is_disabled">{{ trans('lang.save') }}</button>
                    <button class="btn cancel-btn waves-effect waves-grey mob-btn modal-close" @click.prevent="setActive(),setActive2()">{{ trans('lang.cancel') }}</button>
                    <button class="btn materialize-red waves-effect waves-grey mob-btn modal-trigger right-align holiday-delete-btn" data-target="confirm-holiday-delete" v-if="id" @click.prevent="setDeleteId()">{{ trans('lang.delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        props:['id','selectedDate'],
        data(){
            return{
                item:[],
                title:'',
                start_date:'',
                end_date:'',
                error:false,
                is_disabled:false,
                errorInvalid:{
                    title:false,
                    start:false,
                    end:false,
                },
                hidePreloader:'hide',
                preloaderType:'load',
                dateEndError:this.trans('lang.required_input_field'),
            }
        },
        mounted(){
            let instance = this;
            $('#title').on('input', function() {
                instance.errorInvalid.title=false;
            });
            $('#start-date').change(function(){
                instance.errorInvalid.start_date=false;
            });
            $('#end-date').change(function(){
                instance.errorInvalid.end_date=false;
            });
            if(this.id)
            {
                this.readData();
                this.renderDate();
            }
            else
            {
                this.start_date = this.selectedDate;
                this.end_date = this.selectedDate;
                this.renderDate();
                this.$nextTick(function(){
                    M.updateTextFields();
                });
            }
            $('#confirm-holiday-delete').modal({
                    dismissible: true, // Modal can be dismissed by clicking outside of the modal
                    opacity: .5, // Opacity of modal background
                    inDuration: 300, // Transition in duration
                    outDuration: 200, // Transition out duration
                    startingTop: '0', // Starting top style attribute
                    endingTop: '0', // Ending top style attribute
                }
            );
        },
        methods: {
            setDeleteId()
            {
                this.$hub.$emit('getDeleteId', this.id);
            },
            setActive: function()
            {
                this.$emit('setActive', false);
                $('#holiday-modal').modal('close');
            },
            setActive2: function()
            {
                this.$emit('setActive2', false);
                $('#add-holiday-modal').modal('close');
            },
            setPreloader: function(type,action) {
                this.preloaderType = type;
                this.hidePreloader = action;

            },
            setErrorInvalid(){
                this.error=false;
                this.errorInvalid.title=false;
                this.errorInvalid.start_date=false;
                this.errorInvalid.end_date= false;
            },
            setError(value){
                let instance = this;
                instance.$nextTick(function(){
                    instance.error = true;
                    instance.errorInvalid[value] = true;
                });
                instance.setPreloader('load','hide');
            },
            save()
            {
                let instance = this;
                if(this.title==undefined || this.title=='')
                {
                    this.$nextTick(function () {
                        instance.error = true;
                        instance.errorInvalid.title = true;
                        instance.is_disabled = false;
                    })
                    M.updateTextFields();
                    this.setPreloader('load','hide');
                }
                if(this.start_date=='' || this.start_date==undefined)
                {
                    this.$nextTick(function () {
                        if(!instance.error)
                        {
                            instance.error = true;
                        }
                        instance.errorInvalid.start_date = true;
                        instance.is_disabled = false;
                    })
                    this.setPreloader('load','hide');
                }
                if(!this.end_date)
                {
                    this.$nextTick(function () {
                        if(!instance.error)
                        {
                            instance.error = true;
                        }
                        instance.errorInvalid.end_date = true;
                    })
                    this.setPreloader('load','hide');
                }

                if(moment(this.start_date,instance.dateFormat.toUpperCase()).format('YYYY-MM-DD') > moment(this.end_date,instance.dateFormat.toUpperCase()).format('YYYY-MM-DD'))
                {
                    if(instance.end_date === '')
                    {
                        instance.setError('end_date');
                        instance.dateStartError = instance.trans('lang.required_input_field');
                    }
                    else
                    {
                        instance.setError('end_date');
                        instance.dateEndError =  instance.trans('lang.cannot_be_less_than_start_date');
                        instance.is_disabled = false;
                    }
                }

                if(this.title && this.start_date && this.end_date && moment(this.start_date,instance.dateFormat.toUpperCase()).format('YYYY-MM-DD') <= moment(this.end_date,instance.dateFormat.toUpperCase()).format('YYYY-MM-DD'))
                {

                    this.setPreloader('load','');
                    instance.error = true;
                    if(!this.id)
                    {

                        this.axiosPost('/holiday/store',{
                                title: this.title,
                                start_date: moment(this.start_date,instance.dateFormat.toUpperCase()).format('YYYY-MM-DD'),
                                end_date: moment(this.end_date,instance.dateFormat.toUpperCase()).format('YYYY-MM-DD'),
                            },
                            function(response){
                                M.toast({html:instance.trans('lang.holiday_successfully_saved')});
                                instance.setActive();
                                instance.setActive2();
                                instance.is_disabled = false;
                                instance.setPreloader('load','hide');
                                instance.getOffDays();
                                instance.getHolidays();
                            },
                            function (error) {
                                M.toast({html:instance.trans('lang.getting_problems'), classes:'red lighten-1'});
                                instance.setPreloader('load','hide');
                                instance.is_disabled = false;
                            });
                    }
                    else
                    {

                        this.axiosPost('/holidays/'+this.id ,{
                                title: this.title,
                                start_date: moment(this.start_date,instance.dateFormat.toUpperCase()).format('YYYY-MM-DD'),
                                end_date: moment(this.end_date,instance.dateFormat.toUpperCase()).format('YYYY-MM-DD'),
                            },
                            function(response){
                                M.toast({html:instance.trans('lang.holiday_successfully_saved')});
                                instance.setActive();
                                instance.setActive2();
                                instance.is_disabled = false;
                                instance.setPreloader('load','hide');
                                instance.getOffDays();
                                instance.getHolidays();
                            },
                            function (error) {
                                M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                                instance.setPreloader('load','hide');
                                instance.is_disabled = false;
                            });
                    }
                }
            },
            readData() {
                let instance = this;
                instance.setPreloader('load','');

                instance.axiosGet('/holidays/'+this.id,
                function (response) {
                    instance.item = response.data;
                    instance.title = instance.item.title;
                    instance.start_date = instance.item.start_date;
                    instance.end_date = instance.item.end_date;

                    instance.setPreloader('load','hide');
                    setTimeout(function(){
                        M.updateTextFields();
                        instance.renderDate();
                        M.updateTextFields();
                    })

                },function (error) {
                        instance.setPreloader('load','hide');
                    });
            },
            is_disable()
            {
                this.is_disabled = true;
            },
            getOffDays(){
                this.$hub.$emit('getOffDays');
            },
            getHolidays(){
                this.$hub.$emit('getHolidays');
            },
            renderDate()
            {
                let startDate;
                let endDate;
                let instance = this;
                $('#start-date').datepicker({
                    selectMonths: true, // Creates a dropdown to control month
                    selectYears: 70, // Creates a dropdown of 70 years to control year,
                    today: 'Today',
                    clear: 'Clear',
                    close: 'Ok',
                    format: instance.dateFormat,
                    //min: 'Today',
                    closeOnSelect: true // Close upon selecting a date,

                });
                $('#end-date').datepicker({
                    selectMonths: true, // Creates a dropdown to control month
                    selectYears: 70, // Creates a dropdown of 70 years to control year,
                    today: 'Today',
                    clear: 'Clear',
                    close: 'Ok',
                    format: instance.dateFormat,
                    //min: 'Today',
                    closeOnSelect: true // Close upon selecting a date,
                    ,
                });
                if(instance.end_date==undefined)
                {
                    instance.end_date= '';
                }
                $('#start-date').on('change', function () {
                    var start = $(this).val();
                    instance.start_date = start
                    instance.errorInvalid.start = false;
                    if(instance.end_date=='' && instance.start_date)
                    {
                        instance.end_date = instance.start_date
                        instance.$nextTick(function(){
                            M.updateTextFields();
                        });
                    }
                });
                $('#end-date').on('change', function () {
                    var end = $(this).val();
                    instance.end_date = end;
                    instance.errorInvalid.end = false;
                });
                if(instance.start_date!=undefined && instance.end_date!=undefined)
                {
                    var separators = [' ', '\\\+', '-', '\\\(', '\\\)', '\\*', '/', ':', '\\\?'];
                    startDate = instance.start_date.split(new RegExp(separators.join('|'), 'g'));
                    endDate = instance.end_date.split(new RegExp(separators.join('|'), 'g'));;
                }
            },
        }
    }
</script>