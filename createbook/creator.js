//CYOA Creator

$(function(){

	var page = Backbone.Model.extend({
		defaults: {
			id: 0,
			choiceDialog:'place holder',
			parentPage: 0,
			title: 'title',
			content: 'content'
		}
	});

	var bookView = Backbone.View.extend({
		template:  _.template($('#page_tpl').html()), 

		events: {
			"blur .storyText":"contentChanged",
			"click #addChoice":"addChoice",
			"click .create.index": "choiceSwap",
			"blur input.dialog": "dialogChange",
			"click #backAPage": "backAPage"
		},

		contentChanged: function(){
			this.model.bookMark.set("content", $(".storyText").val());
		},

		addChoice: function(){
			this.model.addPage();
		},

		choiceSwap: function(e){
			var pageid = $(e.target).attr("data-id");
			this.model.changePage(parseInt(pageid));
		},

		dialogChange: function(e){
			var pageid = parseInt($(e.target).attr("data-id"));
			var data = $(e.target).val();
			if(data == ""){
				this.model.remove({"id": pageid});
			}
			else {
				var page = this.model.where({"id": pageid})[0];
				page.set("title", data);
			}
		},

		backAPage: function(){
			var parentPage = this.model.bookMark.get("parentPage");
			this.model.changePage(parentPage);
		},

		initialize: function(){
			var that = this;
			$(".container").html(this.$el);
			this.model = new bookCollection();

			this.listenTo(this.model, "update", this.render);
		},

		refreshBook: function(){
			this.model.syncToServer();
			this.render();
		},

		render: function(){
			var that = this;

			//var tempJSON = this.model.bookMark.toJSON();
			//tempJSON.content = tempJSON.content.replace(/\n/g, '<br>');
			
			var choices = this.model.getChoicesFor(this.model.bookMark.get('id'));
			var choicesJSON = [];
			for(var i = 0; i < choices.length; i ++) {
				choicesJSON.push(choices[i].toJSON());
			}
			var tempJSON = {
				link: "42",
				title: this.model.bookMark.get("title"),
				content: this.model.bookMark.get("content"),
				choices: choicesJSON
			}

			console.log(tempJSON);

			this.$el.html(this.template(tempJSON));
			return this;
		}

	});

	var bookIndexer = Backbone.Model.extend({
		model: {},
		url: function(){
			return 'index.php/sync/indexer/';
		},

		initialize: function() {

		},

		syncToServer: function() {


			var response = this.save(this.model,{
				success:function(model, response, options){

					if(response.action == "save")
					{
						console.log("save");
					}
					if(response.action == "update")
					{
						console.log("updated");
					}

				},
				error:function(model, response, options){
					console.error("Could not save to the server.")
				}
			});

			/*
			*In basic terms, the toJSON() is the encapsulator for the data being
			*sent to the server. If we ever want to send anything else on saves,
			*add it in that some how.
			*/
		},
		toJSON: function() {
			properBook = {
				pages: this.model.toJSON(),
				author: "me!",
				title: this.model.title
			}

			this.model.toJSON();
		    return properBook;
		}
	});

	var bookCollection = Backbone.Collection.extend({
		title: "title",
		model: page,
		bookMark: -1,
		indexer: function(){
			return new bookIndexer();
		}(),

		url: '/app',

		initialize: function(){
			var that = this;
			this.bookMark = this.addPage({
				title: "Your first page!",
				content: "This is your first page. Go ahead, add more, and then come back!"
			});
			this.indexer.model = this;
			this.on("remove", function(){that.trigger("update")});
			this.on("change", function(){
				this.syncToServer();
			});
		},

		syncToServer: function() {
			this.indexer.syncToServer();
		},

		addPage: function(pageData){
			var newID = this.length + 1;
			var newPage = new page(pageData);
			if(newID !== 1){
				newPage.set({parentPage: this.bookMark.get("id")});
			}
			newPage.set("id", newID);

			this.add(newPage);
			this.trigger('update');

			return newPage;
		},

		changePage: function(id){
			if(!id){return "ID missing"};
			this.bookMark = this.where({"id": id})[0];
			this.trigger('update');
			return this.bookMark;
		},

		getChoicesFor: function(bookID){
			var choices = this.where({"parentPage": bookID});

			console.log(choices);

			return choices;
		}
	});

	var cyoaApp = new bookView();
	cyoaApp.render();
});