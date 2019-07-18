<template>
   <div>
       <div class="role-close-button modal-close">
           <a href="#" @click.prevent=""><i class="material-icons dp48 grey-text">clear</i></a>
       </div>
       <div class="row  margin-fix">
           <div class="content-parent">
               <div class="content-child">
                   <div class="col s12 m6 l4 offset-m3 offset-l4 center-align">
                       <div class="row">
                           <h5>{{ trans('lang.are_you_sure') }}</h5>
                           <h6>{{ trans('lang.'+message) }}</h6>
                       </div>
                       <div class="row">
                           <button class="modal-action modal-close waves-effect waves-light btn materialize-red mob-btn" type="submit" @click.prevent="buttonAction()">{{ trans('lang.'+firstButtonName) }} </button>
                           <button class="modal-action cancel-btn modal-close waves-effect waves-green btn mob-btn" @click.prevent="">{{ trans('lang.'+secondButtonName) }} </button>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
</template>

<script>
    export default {
       props:['message','firstButtonName','secondButtonName'],
        data()
        {
            return{

            }
        },
        methods:{
           buttonAction()
           {
               this.$emit('confirmationModalButtonAction');
           }
        }
    }
</script>
