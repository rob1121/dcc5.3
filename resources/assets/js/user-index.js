require('./app');

const app = new Vue( {
    el: "#app",

	data: {
    	pagination: {}
	},

	mounted() {
		this.getUsers();
	},

    filters: {
    	capitalize(string)
    	{
    		return _.toUpper(string);
    	},

    	nameCase(name)
    	{
    		return _.map( name.split(" ") , (word) => _.capitalize(word) ).join(" ");
    	}
    },

	methods: {
		remove(user)
		{
		    const delete_route = laroute.route("user.destroy", {user: user.id});
		    this.$http.delete(delete_route).then(
                () => this.deleteItem(this.pagination.data, user),
                error => console.log(error.text())
            );
		},

        deleteItem(collection, item)
        {
            var index = collection.indexOf(item);
            collection.splice(index, 1)
        },

    	getUsers()
		{
    		return this.$http.get(laroute.route("api.search.user"))
				.then(response => this.pagination = response.json());
		}
	}
} );