<template>
    <div id="department--container">

        <input v-for="department in selected" :name="name+'[]'" type="hidden" :value="department">
        <!--query input field search container-->
        <div style="position:relative">
            <i class="add-btn fa fa-plus"
               @click="insertToSelectedItem(query)"
               v-if="showAddButton">
            </i>

            <input type="text"
                   class="form-control input-sm"
                   v-model="query"
                   @keyup.27="hideResultsContainer"
                   @keypress.enter="returnFalse"
                   @focus="displayListBox">
        </div>

        <!--search result container-->
        <div :style="'width:'+resultWidth"
             class="search-result"
             v-if="showListBox"
        >

            <!--department list-->
            <li class="department--item"
                v-for="department in foundQueryInDepartmentsList"
                @click="insertToSelectedItem(department)">
                    <span class="pull-right h6">
                        <i class='fa fa-plus'></i></span>
                        <span class="h6" v-text="department">
                    </span>
            </li>

            <em class="search--not--found text-danger h6" v-if="! hasFoundQueryInDepartmentsList">No department matched your input</em>
        </div>

        <!--department list-->
        <li class="selected--department--item h6" v-for="department in selected">
            <i class='text-right fa fa-remove' @click="removeToSelectedItem(department)"></i>
            <em v-text="department"></em>
        </li>

        <!--fade block-->
        <div id="fade" v-if="showListBox" @click="hideResultsContainer"></div>
    </div>
</template>

<style scoped>

    .add-btn {

        position: absolute;
        z-index: 2;
        top: 50%;
        bottom: 0;
        right: 5px;
        transform: translateY(-50%);
        color: darkgreen;
        cursor: pointer;
    }

    .search-result {

        position: absolute;
        z-index: 2;
        width: 100%;
        border: 1px solid rgba(0,0,0,0.2);
        box-shadow: 0 3px 2px rgba(0,0,0,0.2);
        background: #fff;
        border-bottom-left-radius: 2px;
        border-bottom-right-radius: 2px;
        padding: 0;
    }

    .search-result i.fa.fa-plus {
        position: relative;
        top: 50%;
        right: 5px;
        transform: translateY(-50%);
        display: none;
    }

    .search-result .search--not--found,
    .search-result .department--item {
        padding: 1px 5px;
        margin: 0;
        list-style: none;
        transition: .1s ease-in-out;
    }

    .search-result .department--item,
    .search-result .fa-remove {
        cursor: pointer;
    }

    .search-result .department--item:hover {

        background: #3097D1;
        color: #f5f8fa;
    }

    .search-result .department--item:hover i.fa.fa-plus {
        display: block;
    }


    .selected--department--item {
        padding: 1px 5px;
        margin: 0;
        list-style: none;
        white-space: nowrap;
        transition: .1s ease-in-out;
    }

    .fa-remove {
        color: darkred;
        cursor: pointer;
    }

    .fa-remove:hover{
        transform: scale(1.1);
    }

    .has-error .form-control {
        border-color: #a94442;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.0);
    }

    .has-error .help-block {
        color: #a94442;
    }

    .has-error .help-block,
    .has-error i.fa.fa-plus.add-btn {
        z-index: 2;
        color: #a94442;
    }

    .form-control {
        position: relative;
        z-index: 1;
    }

    #fade {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
    }
</style>

<script>
    import department from "./departmentMixins";
    import { isSuccess,
             setQuery,
             isQueryValid,
             hasQuery,
             hasQueryText } from "./QueryMethods";

    export default {
        data() {
            return {
                resultWidth: 0,
                query: "",
                selected: [],
                showListBox: false,
                showAddButton: false
            }
        },

        mounted() {
            this.setSelected(this.value);
            this.setDepartments(this.departmentsList);
            this.extractUnselectedDepartment();
        },

        props: {
            name: {default: ""},
            departmentsList: {default: []},
            value: {default: []},
        },

        mixins: [department],

        computed: {

            hasFoundQueryInDepartmentsList() {
                return ! _.isEmpty(this.foundQueryInDepartmentsList);
            },

            foundQueryInDepartmentsList() {
                const self = this;
                const departments = _.filter( self.departments, d => d.toLowerCase().includes(self.query));
                return _.orderBy(departments,null,'asc');
            },

            showSearchResultBox() {
                return this.hasQueryText
                        || this.hasUsers
                        || this.hasDepartment;
            },
            hasQuery,
            hasQueryText,
        },

        watch: {
            query() {
                const self = this;
                self.getResults();
                self.displayAddButton();
            }
        },

        methods: {
            isSuccess,
            setQuery,
            isQueryValid,

            displayAddButton() {
                const displayAddButton = ! this.hasFoundQueryInDepartmentsList && this.hasQuery;
                this.setShowAddButton( displayAddButton );
            },


            /**
             * set departments
             * @param departments
             */
            setDepartments(departments) {
                this.departments = JSON.parse(departments);
            },

            setShowAddButton(bool) {
                this.showAddButton = bool;
            },

            getResults() {
                this.setResultWidth( this.containerWidth() );
            },

            displayListBox() {
                this.setShowListBox(true);
                this.getResults();
            },

            /**
             * hide search result container
             */
            hideResultsContainer() {
                this.setQuery("");
                this.setShowAddButton(false);
                this.setShowListBox(false);
            },

            /**
             * adjust search result container width
             * @param width
             */
            setResultWidth(width) {
                this.resultWidth = width + 'px'
            },

            /**
             * get search result container width
             * @returns {number}
             */
            containerWidth() {
                return document.getElementById( 'department--container' )
                               .clientWidth;
            },

            setSelected(departments) {
                    const sanitizedDepartment = _.isEmpty(departments) ? [] : JSON.parse(departments);
                this.selected = _.toArray(sanitizedDepartment);
            },

            /**
             * insert item to selected data
             * @param department
             * @returns {boolean}
             */
            insertToSelectedItem(department) {
                
                if( this.isNotExist( department )) {
                    this.removeToDepartments(department);
                    this.selected.push( department.toUpperCase() );
                }
                
                this.setQuery("");
                this.setShowListBox(false);

            },

            setShowListBox(bool) {
                this.showListBox = bool;
            },

            /**
             * validate department is unique
             * @param department
             * @returns {boolean}
             */
            isNotExist(department) {
                const haystack = _.map(this.selected, dept => dept.toLowerCase());

                return haystack.indexOf(department.toLowerCase()) < 0;
            },

            /**
             * remove user from selected data
             * @param department
             */
            removeToSelectedItem(department) {
                const selectedIndex = this.selected.indexOf(department);
                this.selected.splice(selectedIndex, 1);

                const departmentIndex = this.departmentsList.indexOf(department);
                if(departmentIndex > -1) this.departments.push( department );
            },

            returnFalse(e) {
                e.preventDefault();
            }
        }
    }
</script>