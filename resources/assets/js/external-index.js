require("./app");
import search from "./mixins/search";

const app = new Vue({
	el: "#app",

	data: {
		category: {
			customer_name: category.customer_name
		},

		modalConfirmation: {
		    action: "update",
			category: [],
            indexOfSpecForUpdate: null
		},

        pagination: {}
	},

    mixins: [search],

	mounted() {
		this.$nextTick( () => this.getPagination() )
	},

    filters: {
	    filterReduceMap(customer) {
           return _.reduce(this.pagination.data , (total, item) => {
               if(item.customer_spec_category.customer_name === customer) {
                   for(var x in item.customer_spec_revision) {
                       if(item.customer_spec_revision[x].is_reviewed === 0) total++;
                   }
               }
               return total;
            },0);
        },

        documentLink(specRevision) {
            return laroute.route('external.show', {external:specRevision.customer_spec_id,revision:specRevision.revision});
        },

        routeEditLink(id) {
            return laroute.route("external.edit", {external:id});
        },

        isHasForReview(collection) {

            for(var x in collection)
                if(collection[x].is_reviewed === 0) return false;

            return true;
        }
    },

    methods: {
        getSpecByCategory(category, index) {
            this.setSpecCategory(category);
            this.getPagination();
            this.setActiveMenu(index);
        },

        setSpecCategory(category) {
            this.category = category;
        },

        setActiveMenu(index) {
            this.currentIndex = index;
        },

        getPagination(num = "") {
            var pagination_url = laroute.route('api.search.external');
            this.$http.get(pagination_url, {
                params: { page:num, category:this.category.customer_name }
            }).then(
                (response) => this.setPagination(response.json()),
                () => this.errorDialogMessage()
            );
        },

        setPagination(obj) {
            this.pagination = obj;
            this.closeResultDialog();
        },

        prev() {
            this.getPagination(this.pagination.current_page - 1);
        },

        next() {
            this.getPagination(this.pagination.current_page + 1);
        },

        setModalSpec(spec,action) {
            this.modalConfirmation.category = spec;
            this.modalConfirmation.action = action;
        },

        modalAction() {
            this.modalConfirmation.action === "update"
                ? this.updateSpecStatus()
                : this.removeSpec();
        },

        setUpdateSpec(specRevision) {
            this.indexOfSpecForUpdate = specRevision;
        },

        errorDialogMessage: function () {
            return alert("Oops, server error!. Try refreshing your browser. \n \n if this message box keeps on coming contact system administrator");
        },

        updateSpecStatus() {
            var update_status = laroute.route("external.revision.update", {external:this.modalConfirmation.category.id});

            this.$http.patch(update_status, {is_reviewed: 1,revision:this.indexOfSpecForUpdate.revision})
                .then(
                    () => this.delete(this.modalConfirmation.category.customer_spec_revision, this.indexOfSpecForUpdate),
                    () => this.errorDialogMessage()
                );
        },

        removeSpec() {
            var route_delete = laroute.route("external.destroy", {external:this.modalConfirmation.category.id});

            this.$http.delete(route_delete)
                .then(
                    () => this.delete(this.pagination.data, this.modalConfirmation.category),
                    () => this.errorDialogMessage()
                );
        },

        delete(collection, spec) {
            var index = collection.indexOf(spec);
            collection.splice(index, 1)
        }
    }
});