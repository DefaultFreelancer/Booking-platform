require('./bootstrap');
require('./jquery-3.3.1.min');
require('./hammer.min');
require('./materialize.min');


const Vue = require('vue');
const _ = require('lodash');
window.moment = require('moment');
window.timepicker = require('timepicker');


// an EventHub to share events between components
Vue.prototype.$hub = new Vue();

//basic app data
Vue.prototype.publicPath=window.appConfig.publicPath;
Vue.prototype.dateTimeFormat=window.appConfig.dateTimeFormat;
Vue.prototype.dateFormat=window.appConfig.dateFormat;
Vue.prototype.rowLimit=window.appConfig.knowDefaultRowSettings;
Vue.prototype.currentUserId=window.appConfig.currentUserId;
Vue.prototype.currencySymbol=window.appConfig.currencySymbol;
Vue.prototype.currencyFormat=window.appConfig.currencyFormat;
Vue.prototype.thousandSeparator=window.appConfig.thousandSeparator;
Vue.prototype.decimalSeparator=window.appConfig.decimalSeparator;
Vue.prototype.numberOfDecimal=window.appConfig.numberOfDecimal;
Vue.prototype.appLogo=window.appConfig.appLogo;
Vue.prototype.appName=window.appConfig.appName;
Vue.prototype.currencyCode=window.appConfig.currencyCode;
Vue.prototype.timeFormat=window.appConfig.timeFormat;

// Define a function to use existing laravel language
Vue.prototype.trans = (string, args) => {
    let value = _.get(window.i18n, string);

    _.eachRight(args, (paramVal, paramKey) => {
        value = _.replace(value, `:${paramKey}`, paramVal);
    });
    return value;
};

Vue.component('example-component', require('./components/ExampleComponent.vue'));

//Calendar
Vue.component('calendar', require('./components/calendar/calendar.vue'));
Vue.component('calendar-seven-days', require('./components/home/calendarSevenDays.vue'));

//Login
Vue.component('login-form', require('./components/signin/Login.vue'));

//Register
Vue.component('register-form', require('./components/signup/Register.vue'));

//Reset Password
Vue.component('email-reset', require('./components/passwords/Email.vue'));
Vue.component('reset-password', require('./components/passwords/Reset.vue'));

//Sidebar
Vue.component('side-bar', require('./components/include/Sidebar.vue'));

//Navbar
Vue.component('nav-bar', require('./components/include/Navbar.vue'));

//Profile
Vue.component('profile', require('./components/users/Profile.vue'));
Vue.component('change-password', require('./components/users/account.vue'));

//Dashboard
Vue.component('dashboard', require('./components/Dashboard/Dashboard.vue'));

//Services
Vue.component('service-index', require('./components/service/indexAll.vue'));
Vue.component('createservice-form', require('./components/service/ServiceDetails.vue'));
Vue.component('service-setting', require('./components/service/ServiceSetting.vue'));

//Bookings
Vue.component('booking-index', require('./components/booking/indexAll.vue'));
Vue.component('bookservie-form', require('./components/booking/BookingForm.vue'));
Vue.component('adminbooking-form', require('./components/booking/AdminBooking.vue'));

//Settings
Vue.component('setting-index', require('./components/setting/Index.vue')); //Settings Index
Vue.component('application-setting', require('./components/setting/AppSetting.vue')); //Application
Vue.component('email-form', require('./components/setting/email.vue')); //Email
Vue.component('email-template-list', require('./components/emailtemplate/emailTemplateList.vue')); //Email Template List
Vue.component('email-template-list-details', require('./components/emailtemplate/templateListDetails.vue')); //Email Template Details
Vue.component('offday-setting', require('./components/setting/OffdaySetting.vue')); //Off day
Vue.component('holiday-index', require('./components/holiday/Index.vue')); //Holiday Index
Vue.component('holiday-details', require('./components/holiday/HolidayDetails.vue')); //Holiday Details
Vue.component('client-setting', require('./components/setting/ClientSetting.vue')); //Clients Index
Vue.component('roles', require('./components/roles/Roles.vue')); //Roles index
Vue.component('roles-details', require('./components/roles/RolesDetails.vue')); //Roles Details
Vue.component('user-list', require('./components/users/userList.vue')); //User List
Vue.component('invite-user', require('./components/invitation/invite.vue')); //Clients Invite
Vue.component('user-list-role', require('./components/datatable/UserList.vue')); //User role check;

//Loaders
Vue.component('preloaders', require('./components/preloader/preLoader.vue'));
Vue.component('bar-pre-loader', require('./components/preloader/barPreLoader.vue'));
Vue.component('circle-loader', require('./components/preloader/circleLoader.vue'));

//Filters
Vue.component('date-filter', require('./components/filter/dateFilter.vue'));

Vue.component('all-notification-view', require('./components/notification/AllNotification.vue'));

//Calendar
Vue.component('calendar-seven-days', require('./components/home/calendarSevenDays.vue'));

//Clients components
Vue.component('client-dashboard', require('./components/Dashboard/ClientDashboard.vue'));
Vue.component('client-sidebar', require('./components/include/ClientSidebar.vue'));
Vue.component('clients-index', require('./components/Clients/Index.vue'));
Vue.component('client-details', require('./components/Clients/View.vue'));
Vue.component('client-edit', require('./components/Clients/Edit.vue'));
Vue.component('client-navbar', require('./components/include/ClientNavbar.vue'));
Vue.component('client-booking-list', require('./components/Clients/ClientBookingList.vue'));

//Reports components
Vue.component('report-details', require('./components/reports/Reports.vue'));

// Datatable components
Vue.component('datatable-component', require('./components/datatable/Datatable.vue'));
Vue.component('service-action-component', require('./components/datatable/ServiceTableActions.vue'));
Vue.component('service-copy-component', require('./components/datatable/ServiceTableCopy.vue'));
Vue.component('booking-action-component', require('./components/datatable/BookingTableActions.vue'));
Vue.component('booking-invoice-component', require('./components/datatable/BookingTableInvoice.vue'));
Vue.component('holiday-action-component', require('./components/datatable/HolidayTableActions.vue'));
Vue.component('userlist-action-component', require('./components/datatable/UserListActions.vue'));
Vue.component('client-action-component', require('./components/datatable/ClientActionComponent.vue'));
Vue.component('roles-action-component', require('./components/datatable/RolesActionComponent.vue'));
Vue.component('email-template-action-component', require('./components/datatable/EmailTemplateTableActions.vue'));

// Charts
Vue.component('report-bar-chart', require('./components/charts/reportBarChart.vue'));
Vue.component('line-chart', require('./components/charts/lineChart.vue'));
Vue.component('doughnut-chart', require('./components/charts/doughnutChart.vue'));

Vue.component('booking-details', require('./components/booking/View.vue'));

Vue.component('user-details', require('./components/users/View.vue'));

// LoginRegisterModal
Vue.component('login-register-notice', require('./components/signin/LoginRegisterModal.vue'));

// Payment Setting
Vue.component('payment-index', require('./components/payment/Index.vue'));
Vue.component('payment-details', require('./components/payment/PaymentDetails.vue'));
Vue.component('payment-action-component', require('./components/datatable/PaymentActions.vue'));
Vue.component('due-payment', require('./components/payment/duePayment.vue'));

//Updates
Vue.component('updates', require('./components/updates/updates.vue'));

//Confirmation-modal
Vue.component('confirmation-modal', require('./components/confirmation-modal/confirmationModal.vue'));

//booking payment details
Vue.component('booking-payment-modal', require('./components/booking/BookingPaymentDetails.vue'));


Window.app = new Vue({
    el: '#app',
    data: {
        msg: 'Hello Vue JS'
    }
});
