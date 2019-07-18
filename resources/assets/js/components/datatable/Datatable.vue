<template>
    <div>
        <div class="row margin-fix">
            <div class="row margin-fix">
                <div v-for="(filter, index) in options.filters"
                     :class="{'col s12 m6 l3': (filtersData.length % 3 === 0 && options.search), 'col s12 m4 l4': (filtersData.length % 2 ===0 && options.search), 'col s12 m12 l6 xl6': (filtersData.length % 2 === 0 && !options.search) }">
                    <!-- Date Filter -->
                    <div v-if="filter.type === 'date_range'" :key="filter.key">
                        <date-filter :id="filter.key" :label="filter.title" @setDatefilterValue="getDatefilterValue"></date-filter>
                    </div>

                    <!-- Other Dropdown Filter -->
                    <div class="input-field" v-show="filter.type ==='dropdown'">
                        <select :id="'filter_'+index">
                            <option v-for="option in filter.options" :value="option.value">{{ trans(option.text) }}</option>
                        </select>
                        <label>{{ trans(filter.title) }}</label>
                    </div>

                </div>
                <div class="input-field col" :class="{'s12 m4 l4': filtersData.length>0 && filtersData.length % 2 ===0, 's12 m6 l3': filtersData.length>0 && filtersData.length % 3 ===0, 's12 m4 l4 offset-m8 offset-l8': filtersData.length==0}" v-if="options.search">
                    <input id="search" type="text">
                    <label for="search">{{trans('lang.search')}}</label>
                </div>
            </div>
        </div>
        <div class="row center-align" v-if="searchLoader">
            <div class="modal-loader-parent">
                <div class="modal-loader-child">
                    <circle-loader></circle-loader>
                </div>
            </div>
        </div>
        <div v-else>
            <div :class="{'row':showLoadMore}">
                <div class="responsiveTable">
                    <table class="bordered custom-table-responsive">
                        <thead>
                        <th v-for="column in options.columns" :class="{'right-align': column.type=='component'}">
                            <!-- Check if Column is Sortable -->
                            <a v-if="column.sortable" href="#" @click.prevent="changeSortingKey(column.key), selectedColumn = column.key" class="sortable-table-head">
                                <div class="data-table-sort-parent">
                                    <div class="left-align data-table-sort-child">  {{ trans(column.title) }}</div>
                                    <div class="right-align data-table-sort-child">
                                        <div v-if="columnSortedBy=='ASC' && selectedColumn==column.key"><i class='material-icons dp48 icon-vertically-middle'>arrow_drop_up</i></div>
                                        <div v-if="columnSortedBy=='DESC' && selectedColumn==column.key"><i class='material-icons dp48 icon-vertically-middle'>arrow_drop_down</i></div>
                                        <div v-if="selectedColumn!=column.key" class="data-table-sort-icon"><i class="fa fa-sort"></i></div>
                                    </div>
                                </div>
                            </a>
                            <span v-else>{{ trans(column.title) }}</span>
                        </th>
                        </thead>
                        <tbody v-if="datasets.length > 0">
                        <tr v-for="(data,index) in datasets">
                            <!-- {{data}} -->
                            <td :data-label="trans(column.title)" v-for="column in options.columns" :class="{'table-col-truncate': column.type === 'text' && column.key === 'description'}">

                                <!-- When the column type is clickable_link -->
                                <span v-if="column.type === 'clickable_link' && !column.uniquefield"><a href="#" @click="columnSource(column.source)" >{{data[column.key]}}</a></span>
                                <span v-if="column.type === 'clickable_link' && column.uniquefield"><a href="#" @click="columnSourceKey(column.source+'/'+data[column.uniquefield])">{{data[column.key]}}</a></span>

                                <!-- When the column type is text -->
                                <span v-if="column.type === 'text'" :class="{'table-col-truncate-item': column.type === 'text' && column.key === 'description'}">{{data[column.key]}}</span>
                                <!-- Truncate when the text type is 'description' -->
                                <!-- Check modifier function when the type is custom -->
                                <span v-if="column.type==='custom'" v-html="column.modifier(data[column.key]).value" :class="column.modifier(data[column.key]).class"></span>

                                <!-- Show serially when the column type is array -->
                                <span v-if="column.type === 'array'">
                                <span v-for="value in data[column.key]">{{value}}<br></span>
                            </span>
                                <!-- Show serially when the column type is lang -->
                                <span v-if="column.type === 'language'">
                                {{trans('lang.'+ data[column.key])}}
                            </span>
                                <!-- Load component when the column type is component -->
                                <span v-if="column.type === 'component'">
                                <span v-if="column.modifier">
                                    <component v-if="column.modifier(data[column.key])" v-bind:is="column.componentName" :rowData = data @readData="readData" :rowIndex="index" @setPreloader="setPreloader"></component>
                                </span>
                                <span v-else>
                                    <component v-bind:is="column.componentName" :rowData="data" :rowIndex="index" @readData="readData" @setPreloader="setPreloader"></component>
                                </span>
                            </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="center-align" v-if="datasets.length>0 && showLoadMore">
                <div v-if="buttonPreloader" class="col s2 offset-s5 load-more-button">
                    <circle-loader size="smaller-preloader"></circle-loader>
                </div>
                <button v-else class="btn waves-effect waves-grey grey lighten-5 grey-text mob-btn" @click="increaseLimit()">
                    {{ trans('lang.load_more') }}
                </button>
            </div>
            <div class="row margin-fix" v-if="datasets.length<=0 || showEmptyTableText">
                <div class="col s12">
                    <h6 class="center-align"> {{ trans('lang.didnt_find') }}</h6>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
export default {
    extends: axiosGetPost,
    props: ['options'],
    data(){
        return{
            datasets: [],
            filtersData: [],
            selectedFiltersData: [],
            columnKey: '',
            columnSortedBy: 'ASC',
            showEmptyTableText: false,
            paginationLimit: 10,
            paginationOffset: 0,
            totalResultCount: 0,
            showLoadMore: false,
            searchValue: '',
            alreadyGotRowSettings: false,
            paginationLimitIncreased: false,
            buttonPreloader: false,
            searchLoader:false,
            selectedColumn:'',
        }
    },
    mounted()
    {
        let instance = this;

        if(instance.options.filters) instance.filtersData = instance.options.filters;

        // Search data from table
        instance.searchData();

        $('select').formSelect();
        
        // Get Selected filters Data
        if(instance.filtersData){
            instance.filtersData.forEach(function(element,i){
                $('#filter_'+i).on('change',function(){
                    let value = $(this).val(),
                        key = element.key;
                    
                    if(instance.selectedFiltersData.find(filter => filter.key === key)){
                        instance.selectedFiltersData.find(filter => filter.key === key).value = value;
                    } else {
                        instance.selectedFiltersData.push({key,value})
                    }
                    instance.searchLoader = true;

                    //instance.setPreloader('load','');
                    instance.paginationOffset = 0;
                    instance.readData();
                })
            })
        }
        if(this.options.sortingOrder)
        {
            this.columnSortedBy = this.options.sortingOrder;
        }
        // Read data from DB, before that know settings data for max row limit
        instance.knowDefaultRowSettings();
        this.reloadData();
        this.updateDataAfterDelete();
    },
    methods:{
        columnSourceKey(route){
            let instance = this;
            instance.redirect('/'+route);
        },
        columnSource(route){
            let instance = this;
            instance.redirect('/'+route);
        },
        setPreloader: function(e,f) {
            this.$emit('setPreloader', e,f);
        },
        setId(id){
            this.$emit("setId",id);
        },
        cancelBooking(id){
            this.$emit("cancelBooking",id);
        },
        getDatefilterValue: function(fromDate, toDate, key){
            let instance = this,
                // key = 'date_range',
                value = [fromDate, toDate];

            if(instance.selectedFiltersData.find(filter => filter.key === key)){
                instance.selectedFiltersData.find(filter => filter.key === key).value = value;
            } else {
                instance.selectedFiltersData.push({key,value})
            }
            // this.setPreloader('load','');
            this.searchLoader = true;
            this.readData();
        },
        changeSortingKey(newKey){
            let instance = this;
            if(instance.columnKey == newKey){
                if(instance.columnSortedBy == 'ASC') instance.columnSortedBy = 'DESC';
                else instance.columnSortedBy = 'ASC'
            } else {
                instance.columnSortedBy = 'ASC';
            }
            instance.columnKey = newKey;
            instance.paginationOffset = 0;
            instance.searchLoader = true;
            instance.readData();
        },
        knowDefaultRowSettings(){
            let instance = this;
            if(!instance.alreadyGotRowSettings){
                instance.paginationOffset = 0; // Initialize offset limit also

                instance.paginationLimit = instance.rowLimit;

                    if(instance.options.pagination){
                        if(instance.options.pagination.limit) instance.paginationLimit = instance.options.pagination.limit;
                    }
                    if(!instance.paginationLimit) instance.paginationLimit = 10;
                    instance.alreadyGotRowSettings = true;
                    instance.readData();
            }
        },
        increaseLimit(){
            this.buttonPreloader = true;
            this.paginationOffset += this.paginationLimit;
            
            // this.paginationLimit += 2;
            this.paginationLimitIncreased = true;
            this.readData();
        },
        searchData(){
            let instance = this,
                timer;
            if(instance.options.search){
                $('#search').on('input',function() {
                    let value = $(this).val();
                    instance.searchValue = value;
                    instance.showLoadMore = false;
                    instance.paginationOffset = 0;
                    // instance.setPreloader('load','');
                    if(instance.searchValue){
                        if(timer){
                            clearTimeout(timer);
                        }
                        
                        timer = setTimeout(function(){
                            instance.searchLoader = true;
                            instance.readData();
                        }, 400);
                    } else {
                        instance.searchLoader = true;
                        instance.readData();
                    }
                });
            }
        },
        initOffsetAndLimit(){
            this.paginationOffset = 0;
            this.alreadyGotRowSettings = false;
            this.knowDefaultRowSettings();
        },
        updateDataAfterEdit(id,newData){
            let instance = this;
            instance.datasets.push(newData);
        },
        updateDataAfterDelete(){
            let instance = this;
            this.$hub.$on('updateDataAfterDelete',function (index) {
                instance.datasets.splice(index,1);
            });
        },
        reloadData()
        {
            let instance = this;
            this.$hub.$on('reloadDataTable', function () {
                instance.paginationLimit = 10;
                instance.paginationOffset = 0;
                instance.readData();
            })
        },
        readData(){
            let instance = this,
                url = instance.options.source;

            // Know which field to be sorted
            if(!instance.columnKey){
                if(instance.options.sortedBy) instance.columnKey = instance.options.sortedBy;
                else
                {
                    instance.columnKey = instance.options.columns[0].key;
                    this.selectedColumn = instance.options.columns[0].key
                }
            }
            if(!instance.searchLoader && !instance.buttonPreloader)
            {
                instance.setPreloader('load','');
            }
            instance.axiosPost(url,{
                    columnKey: instance.columnKey,
                    columnSortedBy: instance.columnSortedBy,
                    rowOffset: instance.paginationOffset,
                    rowLimit: instance.paginationLimit,
                    filtersData: instance.selectedFiltersData,
                    searchValue: instance.searchValue
                },
                function(response){
                    instance.searchLoader= false;
                    if(instance.paginationLimitIncreased){
                        let newData = response.data.dataRows;
                        if(newData.length > 0){
                            newData.forEach(function(e){
                                instance.datasets.push(e);
                            })
                        }
                    } else {
                        instance.datasets = response.data.dataRows;
                    }
                    instance.totalResultCount = response.data.count;
                    setTimeout(function () {
                        instance.setPreloader('load','hide');
                    },200)
                    if(instance.buttonPreloader) instance.buttonPreloader = false;

                    if(instance.totalResultCount > instance.datasets.length) instance.showLoadMore = true;
                    else instance.showLoadMore = false;

                    instance.paginationLimitIncreased = false;
                    $('.tooltipped').tooltip();
                },
                function (error) {
                    instance.searchLoader= false;
                    instance.setPreloader('load','hide');

                }
            );
        },
    }
}
</script>