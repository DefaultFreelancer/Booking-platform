<template>
    <!--Start main wrapper-->
    <div class="row margin-fix">
        <div class="calendar-wrapper">
            <div class="row">
                <div class="calender-month center-align">
                    <a class="change-mon-btn bluish-text"
                       @click.prevent="previousMonth(), setDay(), checkLastRow(),holidayList=[]" href="#"> <i
                            class="material-icons">navigate_before</i></a>
                    <div class="month-text bluish-text"> {{thisMonth +" - "+ thisYear}}</div>
                    <a class="change-mon-btn bluish-text"
                       @click.prevent="nextMonth(), setDay(), checkLastRow(),holidayList=[]" href="#"> <i
                            class="material-icons">navigate_next</i></a>
                </div>
            </div>
            <div class="calendar-week-row">
                <div class="calendar-week-col" v-for="(weekDay,index) in weekDays"
                     :class="{'last-col-border': index==6, 'isDisabled':isDisabledWeek(weekDay,offdays)}">
                    {{weekDay}}
                </div>
            </div>
            <div class="calendar-date-row">
                <div class="calendar-date-col"
                     :class="{'last-col-border': checkLastCol(n)}"
                     v-for="n in lastDay">
                    <a class="calendar-date-clickable bluish-text"
                       :class="{'isDisabled': isDisabled((n-a),offdays,renderDayName(n-a)) ,'modal-trigger':!checkHolidayForAnimate(thisYear+'-'+ thisMonthShort+'-'+(n-a)) && (n-a)>0 ,'isHoliday': checkHolidays(thisYear+'-'+ thisMonthShort+'-'+(n-a))}"
                       href="#add-holiday-modal"
                       @click.prevent="triggerHolidayModal(thisYear+'-'+ thisMonthShort+'-'+(n-a)),getActive2(true)">
                        <div class="hover-animate"
                             v-if="!checkHolidayForAnimate(thisYear+'-'+ thisMonthShort+'-'+(n-a)) && (n-a)>0"></div>
                        <div class="event-details bluish-text" v-if="n-a > 0">
                            <a class='modal-trigger holiday-title holiday-results truncate' href="#holiday-modal"
                               @click.prevent="setId( holiday.id)"
                               v-for="holiday in checkHolidays(thisYear+'-'+ thisMonthShort+'-'+(n-a))"> {{
                                holiday.title }} </a>
                        </div>
                        <div class="calendar-date" :class="{'hide': n-a <=0, 'today-date': renderTodayColor(n-a)}">{{
                            n-a }}
                        </div>
                        <div class="calendar-day grey-text darken-4" :class="{'hide': windowWidth>1025}">
                            {{renderDayName(n-a)}}
                        </div>
                    </a>
                </div>
                <div class="calendar-date-col" v-if="lastDay==37 && windowWidth > 1024" v-for="m in 5">
                    <a class="calendar-date-clickable isDisabled">
                    </a>
                </div>
                <div class="calendar-date-col" v-if="lastDay==36 && windowWidth > 1024" v-for="m in 6">
                    <a class="calendar-date-clickable isDisabled">
                    </a>
                </div>
                <div class="calendar-date-col" v-if="lastDay==34 && windowWidth > 1024" v-for="m in 1">
                    <a class="calendar-date-clickable isDisabled">
                    </a>
                </div>
                <div class="calendar-date-col " v-if="lastDay==33 && windowWidth > 1024" v-for="m in 2">
                    <a class="calendar-date-clickable isDisabled">
                    </a>
                </div>
                <div class="calendar-date-col" v-if="lastDay==32 && windowWidth > 1024" v-for="m in 3">
                    <a class="calendar-date-clickable isDisabled">
                    </a>
                </div>
                <div class="calendar-date-col" v-if="lastDay==31 && windowWidth > 1024" v-for="m in 4">
                    <a class="calendar-date-clickable isDisabled">
                    </a>
                </div>
                <div class="calendar-date-col" v-if="lastDay==30 && windowWidth > 1024" v-for="m in 5">
                    <a class="calendar-date-clickable isDisabled">
                    </a>
                </div>
                <div class="calendar-date-col" v-if="lastDay==29 && windowWidth > 1024" v-for="m in 6">
                    <a class="calendar-date-clickable isDisabled">
                    </a>
                </div>
                <div class="calendar-date-col" v-if="lastDay==31 && windowWidth > 480 && windowWidth < 1025"
                     v-for="m in 2">
                    <a class="calendar-date-clickable isDisabled">
                    </a>
                </div>
                <div class="calendar-date-col" v-if="lastDay==29 && windowWidth > 480 && windowWidth < 1025"
                     v-for="m in 1">
                    <a class="calendar-date-clickable isDisabled">
                    </a>
                </div>
                <div class="calendar-date-col" v-if="lastDay==28 && windowWidth > 480 && windowWidth < 1025"
                     v-for="m in 2">
                    <a class="calendar-date-clickable isDisabled">
                    </a>
                </div>
            </div>
        </div>
        <!-- Modal Structure -->
        <div id="holiday-modal" class="modal modal-layout">
            <holiday-details v-if="isActive" @setActive="getActive" :id="selectedHolidayId"
                             :selectedDate="selectedDate"></holiday-details>
        </div>
        <div id="add-holiday-modal" class="modal modal-layout">
            <holiday-details v-if="isActive2" @setActive2="getActive2" :id="selectedHolidayId"
                             :selectedDate="selectedDate"></holiday-details>
        </div>
        <!-- End Modal -->
    </div>
    <!--End main wrapper-->
</template>
<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';

    export default {
        extends: axiosGetPost,
        props: ['sessionstatus'],
        data() {
            return {
                months: moment.months(),
                today: moment().format("D"),
                thisMonth: moment().format("MMMM"),
                thisMonthShort: moment().format("MM"),
                thisYear: moment().format("YYYY"),
                weekDays: moment.weekdays(),
                firstDay: parseInt(moment().startOf('month').format('D')),
                lastDay: parseInt(moment().endOf('month').format('D')),
                firstWeekDay: moment().startOf('month').format("dddd"),
                dayName: '',
                counter: 0,
                a: 0,
                hideContent: '',
                windowWidth: 0,
                newLastday: 0,
                flag: undefined,
                offdays: [],
                Holidays: [],
                selectedHolidayId: '',
                selectedDate: '',
                preloaderType: '',
                hidePreloader: '',
                isActive: false,
                isActive2: false,
                holidayList: [],
                isDisabled: function (date, offDays, dayName) {
                    if (date <= 0) {
                        return true;
                    }
                    return this.checkOffDays(offDays, dayName);
                },
                isDisabledWeek: function (weekNames, offDays) {
                    return this.checkOffDays(offDays, weekNames);
                },

                setDay: function () {
                    if (this.windowWidth < 1025) {
                        this.a = 0;
                        if (this.newLastday > 0) {
                            this.lastDay = this.newLastday;
                        }
                        else {
                            this.lastDay
                        }
                        this.flag = 1;
                    }
                    else {
                        if (this.flag == 1) {
                            if (this.firstWeekDay == 'Sunday') {
                                this.a = 0;
                                this.lastDay += this.a;
                            }
                            else if (this.firstWeekDay == 'Monday') {
                                this.a = 1;
                                this.lastDay += this.a;

                            }
                            else if (this.firstWeekDay == 'Tuesday') {
                                this.a = 2;
                                this.lastDay += this.a;
                            }
                            else if (this.firstWeekDay == 'Wednesday') {
                                this.a = 3;
                                this.lastDay += this.a;
                            }
                            else if (this.firstWeekDay == 'Thursday') {
                                this.a = 4;
                                this.lastDay += this.a;
                            }
                            else if (this.firstWeekDay == 'Friday') {
                                this.a = 5;
                                this.lastDay += this.a;
                            }
                            else if (this.firstWeekDay == 'Saturday') {
                                this.a = 6;
                                this.lastDay += this.a;

                            }
                            this.flag = 0;
                        }
                    }
                },
            }
        },
        mounted() {
            let instance = this;
            this.setDay();
            this.getWindowWidth();
            this.$nextTick(function () {
                window.addEventListener('resize', this.getWindowWidth);
                this.getWindowWidth();
            });
            this.checkLastRow();
            instance.getOffDays();
            instance.getHolidays();
            this.$hub.$on('getOffDays', function () {
                instance.getOffDays();
            });
            this.$hub.$on('getHolidays', function () {
                instance.getHolidays();
            });
            this.$hub.$on('triggerHolidayModal', function (date) {
                instance.triggerHolidayModal(date);
            });
            this.$hub.$on('getActive', function (isActive) {
                instance.getActive(isActive);
            });
            $('#holiday-modal').modal(
                {
                    inDuration: 300, // Transition in duration
                    outDuration: 200, // Transition out duration
                    complete: function () {
                        instance.getActive(false);
                        instance.selectedHolidayId = null;

                    }, onCloseEnd: function () {
                        instance.isActive = false;
                    }
                }
            );
            $('#add-holiday-modal').modal(
                {
                    inDuration: 300, // Transition in duration
                    outDuration: 200, // Transition out duration
                    complete: function () {
                        instance.getActive2(false);
                        instance.selectedHolidayId = null;
                        instance.selectedDate = undefined;
                    }, onCloseEnd: function () {
                        instance.isActive2 = false;
                    }
                }
            );
        },
        methods: {
            setId(id) {
                this.selectedHolidayId = id;
                this.getActive(true);
                this.selectedDate = undefined;
            },
            getActive(isActive) {
                this.isActive = isActive;
            },
            getActive2(isActive2) {
                this.isActive2 = isActive2;
            },
            checkOffDays(offDays, dayName) {
                let hasOffDay;
                offDays.forEach(function (offDay) {
                    if (offDay == 1 && dayName == 'Sunday') {
                        hasOffDay = true;
                    }
                    if (offDay == 2 && dayName == 'Monday') {
                        hasOffDay = true;
                    }
                    if (offDay == 3 && dayName == 'Tuesday') {
                        hasOffDay = true;
                    }
                    if (offDay == 4 && dayName == 'Wednesday') {
                        hasOffDay = true;
                    }
                    if (offDay == 5 && dayName == 'Thursday') {
                        hasOffDay = true;
                    }
                    if (offDay == 6 && dayName == 'Friday') {
                        hasOffDay = true;
                    }
                    if (offDay == 7 && dayName == 'Saturday') {
                        hasOffDay = true;
                    }
                });
                return hasOffDay;
            },
            checkHolidays(date) {
                let array = [];
                let tempArray = [];
                let instance = this;
                this.Holidays.forEach(function (holiday) {
                    if (moment(date, "YYYY-MM-DD") >= moment(holiday.start_date, "YYYY-MM-DD") && moment(date, "YYYY-MM-DD") <= moment(holiday.end_date, "YYYY-MM-DD")) {
                        tempArray = {'id': holiday.id, 'title': holiday.title};
                        array.push(tempArray);
                        instance.holidayList[date] = array;
                    }
                    else {

                    }
                });
                return instance.holidayList[date];
            },
            checkHolidayForAnimate(date) {
                var Animate = false;
                this.Holidays.forEach(function (holiday) {
                    if (moment(date, "YYYY-MM-DD") >= moment(holiday.start_date, "YYYY-MM-DD") && moment(date, "YYYY-MM-DD") <= moment(holiday.end_date, "YYYY-MM-DD")) {
                        Animate = true;
                    }
                });
                return Animate;
            },
            setPreloader: function (type, action) {
                this.$emit('setPreloader', type, action);
            },
            checkLastCol(n) {
                if (n == 7 && this.windowWidth > 1024 || n == 14 && this.windowWidth > 1024 || n == 21 && this.windowWidth > 1024
                    || n == 28 && this.windowWidth > 1024 || n == 35 && this.windowWidth > 1024) {
                    return true;
                }
                if (n == 3 && this.windowWidth > 480 && this.windowWidth < 1025 || n == 6 && this.windowWidth > 480 && this.windowWidth < 1025
                    || n == 9 && this.windowWidth > 480 && this.windowWidth < 1025 || n == 12 && this.windowWidth > 480 && this.windowWidth < 1025
                    || n == 15 && this.windowWidth > 480 && this.windowWidth < 1025 || n == 18 && this.windowWidth > 480 && this.windowWidth < 1025
                    || n == 21 && this.windowWidth > 480 && this.windowWidth < 1025 || n == 24 && this.windowWidth > 480 && this.windowWidth < 1025
                    || n == 27 && this.windowWidth > 480 && this.windowWidth < 1025 || n == 30 && this.windowWidth > 480 && this.windowWidth < 1025) {
                    return true;
                }
            },
            checkLastRow() {
                let instance = this;
                this.$nextTick(function () {
                    var classValue = $('.calendar-date-col').length;
                    if (instance.windowWidth > 1024) {
                        if (classValue == 35) {
                            $("div.calendar-date-col:nth-of-type(28)").nextAll("div.calendar-date-col").addClass('last-row-border');
                        }
                        else if (classValue == 42) {
                            $("div.calendar-date-col:nth-of-type(35)").nextAll("div.calendar-date-col").addClass('last-row-border');
                        }
                    }
                    else if (instance.windowWidth > 480 && instance.windowWidth < 1025) {
                        if (classValue == 33) {
                            $("div.calendar-date-col:nth-of-type(30)").nextAll("div.calendar-date-col").addClass('last-row-border');
                        }
                        if (classValue == 30) {
                            $("div.calendar-date-col:nth-of-type(27)").nextAll("div.calendar-date-col").addClass('last-row-border');
                        }
                    }
                    else {
                        $("div.calendar-date-col:last-of-type").addClass('last-row-border');
                    }
                });
            },
            previousMonth: function () {
                this.counter--
                this.thisMonth = moment().add(this.counter, 'months').format('MMMM');
                this.thisMonthShort = moment().add(this.counter, 'months').format('MM');
                this.newLastday = parseInt(moment().add(this.counter, 'months').endOf('month').format('D'));
                this.lastDay = this.newLastday;
                this.thisYear = moment().add(this.counter, 'months').format("YYYY");
                this.firstWeekDay = moment().add(this.counter, 'months').startOf('month').format("dddd");

                this.flag = 1;
            },
            nextMonth: function () {
                this.counter++
                this.thisMonth = moment().add(this.counter, 'months').format('MMMM');
                this.thisMonthShort = moment().add(this.counter, 'months').format('MM');
                this.newLastday = parseInt(moment().add(this.counter, 'months').endOf('month').format('D'));
                this.lastDay = this.newLastday;
                this.thisYear = moment().add(this.counter, 'months').format("YYYY");
                this.firstWeekDay = moment().add(this.counter, 'months').startOf('month').format("dddd");

                this.flag = 1;

            },
            triggerHolidayModal(selectedDate) {
                let instance = this;
                if (!this.checkHolidayForAnimate(selectedDate)) {
                    this.selectedDate = selectedDate;
                }

            },

            renderDayName: function (day) {
                return moment(day + "/" + this.thisMonth + "/" + this.thisYear, "DD/MMMM/YYYY").format('dddd');
                return this.dayName = moment(day + "/" + this.thisMonth + "/" + this.thisYear, "DD/MMMM/YYYY").format('dddd');
            },
            renderTodayColor(day) {
                var date = day + '-' + this.thisMonthShort + '-' + this.thisYear;
                if (date == moment().format("D-MM-YYYY")) {
                    return true
                }
                else return false;
            },
            getWindowWidth(event) {
                this.windowWidth = document.documentElement.clientWidth;
                this.setDay();

            },
            getOffDays() {
                let instance = this;
                instance.setPreloader('load', '');
                this.axiosGet('/offdaysdata',
                    function (response) {
                        instance.offdays = response.data.offday_setting;
                        setTimeout(function () {
                            instance.setPreloader('load', 'hide');
                            instance.checkLastRow();
                            instance.holidayList = [];
                        })
                    },
                    function (response) {
                        instance.setPreloader('load', 'hide');
                        instance.checkLastRow();
                    },
                );
            },
            getHolidays() {
                let instance = this;
                instance.setPreloader('load', '');
                this.axiosGet('/holidays',
                    function (response) {
                        instance.Holidays = response.data;
                        setTimeout(function () {
                            instance.holidayList = [];
                            instance.setPreloader('load', 'hide');
                            instance.checkLastRow();
                        })
                    },
                    function (response) {
                        instance.holidayList = [];
                        instance.setPreloader('load', 'hide');
                        instance.checkLastRow();
                    },
                );
            }
        },
        beforeDestroy() {
            window.removeEventListener('resize', this.getWindowWidth);
        },


    }

</script>