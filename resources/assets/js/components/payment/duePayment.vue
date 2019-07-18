<template>
    <div>
        <div class="row margin-fix modal-layout-header">
            <div class="col s10">
                <h5 class="bluish-text no-margin">{{ trans('lang.due_payment') }}</h5>
            </div>
            <div class="col s2 right-align">
                <a href="#" class="modal-close" @click.prevent="getActive(0)"><i class="material-icons dp48 grey-text icon-vertically-middle">clear</i></a>
            </div>
        </div>
        <div class="modal-loader-parent"  v-if="hidePreloader!='hide'">
            <div class="modal-loader-child">
                <preloaders :loderType="preloaderType"></preloaders>
            </div>
        </div>
        <div class="modal-layout-content" v-show="hidePreloader=='hide'">
            <form>
                <div class="row">
                    <div class="col col s12 m6 offset-m3">
                        <p>{{ trans('lang.booking_id') }}: #{{  paymentDetails.id }}</p>
                        <p v-if="paymentDetails.status=='confirmed'">{{ trans('lang.status') }}: <span class="booking-status green">{{ trans('lang.'+paymentDetails.status+'_')}}</span></p>
                        <p v-if="paymentDetails.status=='pending'">{{ trans('lang.status') }}: <span class="booking-status orange">{{ trans('lang.'+paymentDetails.status)}}</span></p>
                        <p>{{ trans('lang.bill') }}: {{ paymentDetails.booking_bill}}</p>
                        <p>{{ trans('lang.due') }}: {{ paymentDetails.payment_stat}}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12 m6 offset-m3">
                        <select id="payment-methods">
                            <option value="" disabled selected>{{ trans('lang.choose_one') }}</option>
                            <option v-for="paymentMethod in paymentMethods" :value="paymentMethod.id"> {{paymentMethod.title}}</option>
                        </select>
                        <label for="payment-methods">{{ trans('lang.payment_methods') }}</label>
                    </div>
                </div>
                <div class="row margin-fix">
                    <div v-if="decimalSeparator == '.'" class="input-field col s12 m6 offset-m3">
                        <input v-model="dueClearAmount" name="price" id="price" type="text" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46">
                        <label for="price">{{ trans('lang.payment_amount') }}</label>
                    </div>
                    <div v-if="decimalSeparator == ','" class="input-field col s12 m6 offset-m3">
                        <input v-model="dueClearAmount" name="price" id="priceComma" type="text" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57 || event.charCode == 44">
                        <label for="priceComma">{{ trans('lang.payment_amount') }}</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m6 offset-m3">
                        <div class="time-slot-button">
                            <button class="btn waves-effect waves-light bluish-back mob-btn" @click.prevent="clearDueBill()" :disabled="selectedPaymentMethodId=='' || dueClearAmount<=0"> {{ trans('lang.pay') }} </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        props:['booking_id'],
        data(){
            return{
                preloaderType:'',
                hidePreloader:'',
                paymentDetails:'',
                paymentMethods:'',
                selectedPaymentMethodId:'',
                dueClearAmount:1,
            }
        },
        created()
        {
            this.readData();
        },
        mounted()
        {
            $('#payment-methods').formSelect();
        },
        methods:{

            getActive(isActive){
                this.$hub.$emit("getActive", isActive);
            },
            setPreloader: function(type,action) {
                this.preloaderType = type;
                this.hidePreloader = action;
            },
            readData()
            {
                let instance = this;
                this.setPreloader('load','');

                instance.axiosGet('/payment/payment-details/'+instance.booking_id,
                    function (response) {
                        instance.paymentDetails = response.data.paymentDetails;
                        instance.paymentMethods = response.data.paymentMethods;
                        instance.setPreloader('load','hide');
                        instance.dueClearAmount = accounting.unformat(instance.paymentDetails.payment_stat, instance.decimalSeparator);
                        setTimeout(function () {
                            $('#payment-methods').formSelect();
                            instance.$nextTick(function () {
                                M.updateTextFields();
                            });
                            $('#payment-methods').on('change',function() {
                                let value = $(this).val();
                                instance.selectedPaymentMethodId = value;
                            });
                        });

                    },function (response)
                    {
                        instance.setPreloader('load','hide');
                        M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                    });
            },
            clearDueBill()
            {
                let instance = this;
                if(this.dueClearAmount>0 && this.selectedPaymentMethodId)
                {
                    this.setPreloader('load','');
                    this.axiosPost('/payment/payment-details',{
                            booking_id: this.booking_id,
                            bill: this.dueClearAmount,
                            method_id: this.selectedPaymentMethodId,
                        },
                        function(response){
                            instance.setPreloader('load','hide');
                            $('#due-payment-modal').modal('close');
                            instance.$hub.$emit('reloadDataTable');
                            instance.$hub.$emit('getNewData');
                            M.toast({html:instance.trans('lang.payment_done_successfully')});
                        },
                        function (error) {
                            instance.setPreloader('load','hide');
                            M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                        });
                }
                else {
                    M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                }
            },
        },
    }
</script>