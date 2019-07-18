<template>
    <div>
        <div class="row margin-fix modal-layout-header">
            <div class="col s10">
                <h5 class="bluish-text no-margin">{{ trans('lang.payment_details') }}</h5>
            </div>
            <div class="col s2 right-align">
                <a href="#" class="modal-close"  @click.prevent="setActive">
                    <i class="material-icons dp48 grey-text icon-vertically-middle">clear</i>
                </a>
            </div>
        </div>
        <div class="modal-loader-parent"  v-if="hidePreloader!='hide'">
            <div class="modal-loader-child">
                <preloaders :loderType="preloaderType"></preloaders>
            </div>
        </div>

        <div class="main-layout-card-content" v-show="hidePreloader=='hide'">

            <div class="row">
                <div class="col s6">{{ trans('lang.booking_id') }}</div>
                <div class="col s6" style="width: 40%;">#{{ payment.booking_id }}</div>
            </div>

            <div class="row">
                <div class="col s6"> {{ trans('lang.amount') }}</div>
                <div class="col s6" style="width: 40% !important;">{{ payment.bill }}</div>
            </div>
            <div class="row" v-if="payment.payment_method">
                <div class="col s6"> {{ trans('lang.payment_method') }}</div>
                <div class="col s6" style="width: 40% !important;">{{ payment.payment_method }}</div>
            </div>
            <div class="row" v-if="payment.first_name">
                <div class="col s6"> {{ trans('lang.received_by') }}</div>
                <div class="col s6" style="width: 40% !important;">{{ payment.first_name +' '+ payment.last_name }}</div>
            </div>
            <div class="row">
                <div class="col s6"> {{ trans('lang.created_at') }}</div>
                <div class="col s6" style="width: 40% !important;">{{ payment.created }}</div>
            </div>

        </div>

    </div>
</template>
<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default
    {
        extends: axiosGetPost,
        props:['payment_method_id'],
        data()
        {
            return{
                payment:{},
                hidePreloader:'',
                preloaderType:'load',
            }
        },
        created()
        {
            this.getPaymentDetails();
        },
        methods:
        {
            setActive: function()
            {
                this.$emit('setActive', false);
                $('#payment-modal').modal('close');
            },
            setPreloader: function(type,action) {
                this.preloaderType = type;
                this.hidePreloader = action;

            },

            getPaymentDetails()
            {
                let instance = this;
                instance.setPreloader('load','');
                instance.axiosGet('/booking-payment/'+instance.payment_method_id,
                    function (response)
                    {
                        instance.payment = response.data;
                        instance.setPreloader('load','hide');
                        setTimeout(function () {
                            M.updateTextFields();
                            $('select').formSelect();
                        })
                    },function (response)
                    {
                        instance.setPreloader('load','hide');
                    })

            }
        }
    }
</script>