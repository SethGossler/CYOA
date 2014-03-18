//CYOA Creator

$(function(){

	var page = Backbone.Model.extend({
		defaults: {
			id: 0,
			choiceDialog:'place holder',
			parentPage: 0,
			title: 'page title',
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
			"click #backAPage": "backAPage",
			"blur input#bookTitle": "titleChange",
			"blur input#pageTitle": "pageTitleChange"
		},

		titleChange: function(e){
			this.model.title = $(e.target).val();
		},

		pageTitleChange: function(e){
			this.model.bookMark.set("title", $(e.target).val());
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
				id: this.model.id,
				title: this.model.title,
				pageTitle: this.model.bookMark.get("title"),
				content: this.model.bookMark.get("content"),
				choices: choicesJSON
			}

			this.$el.html(this.template(tempJSON));
			return this;
		}

	});

	var bookIndexer = Backbone.Model.extend({
		model: {},
		url: function(){
			return 'index.php/sync/indexer/';
		},

		syncToServer: function() {

			var that = this;
			var response = this.save(this.model,{
				success:function(model, response, options){
					if(response.result == "success"){
						that.model.id = response.id;
					}
				},
				error:function(model, response, options){
					console.error("Could not save to the server.")
				}
			});
			
		},
		toJSON: function() {
			properBook = {
				pages: this.model.toJSON(),
				author: 42,
				title: this.model.title,
				id: this.model.id
			}

			this.model.toJSON();
		    return properBook;
		}
	});

	var bookCollection = Backbone.Collection.extend({
		title: "book title",
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
			return choices;
		}
	});

	var cyoaApp = new bookView();
	cyoaApp.render();
});