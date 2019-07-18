<template>
    <div>
        <div class="navbar-text hide-on-med-and-down">
            {{ trans('lang.settings') }}
        </div>
        <div class="admin-layout-margin-for-col">
            <div class="row margin-fix">
                <div class="col s12 m12 l12 xl2 col-right-padding">
                    <div class="main-layout-card">
                        <div class="main-layout-card-header">
                            <div class="main-layout-card-content-wrapper">
                                <div class="main-layout-card-header-contents">
                                    <h5 class="bluish-text no-margin">{{ trans('lang.settings') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="main-layout-card-content no-padding">
                            <div class="collection max-content-height no-border no-margin">
                                <ul id="settings-list">
                                    <li v-for="tab in tabs" v-if="isVisible(tab.name)">
                                        <a href="#" class="collection-item no-active-border"
                                           :class="{'active-border':isSelectedTab(tab.name)}"
                                           @click.prevent="selectTab(tab.name, tab.component)">
                                            {{ trans(tab.lang) }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col s12 m12 l12 xl10">
                    <transition name="slide-fade" mode="out-in">
                        <component v-if="this.componentName" v-bind:is="this.componentName"></component>
                    </transition>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['app_settings', 'email_settings', 'email_templates', 'off_day', 'holiday', 'roles', 'users', 'client_settings', 'payment_settings', 'updates'],
        data() {
            return {
                selectedTab: null,
                componentName: null,
                tabs: [
                    {name: "app_settings", lang: "lang.application", component: "application-setting"},
                    {name: "email_settings", lang: "lang.emails", component: "email-form"},
                    {name: "email_templates", lang: "lang.email_templates", component: "email-template-list"},
                    {name: "off_day", lang: "lang.off_day", component: "offday-setting"},
                    {name: "holiday", lang: "lang.holidays", component: "holiday-index"},
                    {name: "client_settings", lang: "lang.clients", component: "client-setting"},
                    {name: "roles", lang: "lang.roles", component: "roles"},
                    {name: "users", lang: "lang.users", component: "user-list"},
                    {name: "payment_settings", lang: "lang.payment_methods", component: "payment-index"},
                    {name: "updates", lang: "lang.updates", component: "updates"}
                ],
                isVisible: function (tabName) {
                    return (this[tabName] == "1");
                },
                isSelectedTab: function (tabName) {
                    return (tabName === this.selectedTab);
                }
            }
        },
        methods: {
            selectTab: function (tabName, componentName) {
                this.selectedTab = tabName;
                this.componentName = componentName;
            },
            initSelectedTab: function () {
                let instance = this;

                this.tabs.forEach(function (tab) {
                    if (!instance.selectedTab && instance.isVisible(tab.name)) {
                        instance.selectTab(tab.name, tab.component);
                    }
                });
            }
        },
        mounted() {
            this.initSelectedTab();
            // Materialize.showStaggeredList('#settings-list')
        }
    }
</script>
