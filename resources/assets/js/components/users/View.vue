<template>
    <div>
        <div class="navbar-text hide-on-med-and-down">
            {{ trans('lang.booking_history') }}
        </div>
        <div class="admin-layout-margin">
           <div class="row margin-fix">
               <div class="col s12 m12 l4 xl3 no-padding">
                   <div class="main-layout-card">
                       <div class="main-layout-card-header-with-button">
                           <div class="main-layout-card-content-wrapper">
                               <div class="main-layout-card-header-contents">
                                   <div class="row margin-fix center-align profile-image-container">
                                       <img class="responsive-img profile-image materialboxed" :src="publicPath+'/uploads/profile/'+user.avatar" alt="">
                                   </div>
                               </div>
                           </div>
                       </div>
                       <div class="main-layout-card-content">
                           <div class="row margin-fix center-align">
                               <h5 class="bluish-text">{{user.first_name+' '+user.last_name }}</h5>
                               <h6>
                                   <i class="la la-envelope"></i>
                                   {{ user.email }}
                               </h6>
                               <h6 v-if="user.phone !=null">
                                   <i class="la la-phone"></i>
                                   {{ user.phone }}
                               </h6>
                           </div>
                       </div>
                   </div>
               </div>
               <div class="col s12 m12 l8 xl9 no-padding">
                   <div class="main-layout-card main-layout-card-second">
                       <div class="main-layout-card-header">
                           <div class="main-layout-card-content-wrapper">
                               <div class="main-layout-card-header-contents">
                                   <h5 class="bluish-text no-margin">{{ trans('lang.booking_history') }}</h5>
                               </div>
                           </div>
                       </div>
                       <div class="main-layout-card-content">
                           <div class="row margin-fix">
                               <div class="modal-loader-parent"  v-if="hidePreloader!='hide'">
                                   <div class="modal-loader-child">
                                       <preloaders :loderType="preloaderType"></preloaders>
                                   </div>
                               </div>
                               <datatable-component :options="tableOptions" @setPreloader="getPreloader" v-show="hidePreloader=='hide'"></datatable-component>
                           </div>
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
        props:['user'],
        data(){
            let instance = this;
            return{

                hidePreloader:'',
                preloaderType:'load',

                tableOptions: {
                    tableName: 'booking',
                    columns: [
                        {title: 'lang.id', key: 'id', type: 'clickable_link', source:'/booking-details', uniquefield:'id', sortable: true},
                        {title: 'lang.service', key: 'title', type: 'text', sortable: true},
                        {title: 'lang.status', key: 'status', type: 'custom', sortable: true,
                            modifier: function(rowData){
                                if(rowData == 'confirmed') return {value: instance.trans('lang.confirmed_'), class: "booking-status green"};
                                else if(rowData == 'pending') return {value: instance.trans('lang.pending'), class: "booking-status orange"};
                                else if(rowData == 'canceled') return {value: instance.trans('lang.canceled'), class: "booking-status grey"};
                            }
                        },
                        {title: 'lang.book_date', key: 'booking_date', type: 'text', sortable: true},
                        {title: 'lang.time', key: 'booking_time', type: 'array', sortable: false},
                        {title: 'lang.quantity', key: 'quantity', type: 'text', sortable: true},

                        {title: 'lang.bill', key: 'booking_bill', type: 'text', sortable: true},

                    ],
                    source: '/client-bookings/'+this.user.id,
                    //sortedBy: 'title',
                    search: false,
                    filters: [
                        {title: 'lang.status', key: 'status', type: 'dropdown', options: [
                                {text: 'lang.all', value: 0, selected: true},
                                {text: 'lang.confirmed_', value: 1},
                                {text: 'lang.pending', value: 2},
                                {text: 'lang.canceled', value: 3}
                            ]},
                        {title: 'lang.date_range', key: 'date_range', type: 'date_range'}
                    ]
                }
            }
        },
        mounted(){
            $('.materialboxed').materialbox();
        },
        methods: {

            getPreloader: function(type,action){
                this.preloaderType = type;
                this.hidePreloader = action;
            },
            bookingDetails(){
                let instance = this;
                instance.$hub.$on('bookingDetails', function(id){
                    this.axiosGet('/booking-details/'+id,
                        function(response){

                        },
                        function (response) {
                        },
                    );
                     instance.redirect("/booking-details/"+id);
                })
            },

            localTime(date_time)
            {
                return moment(date_time + ' Z', 'YYYY-MM-DD HH:mm:ss Z', true).format(this.dateTimeFormat);
            }
        }
    }
</script>