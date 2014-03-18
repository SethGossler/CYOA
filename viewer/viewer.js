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
		template:  _.template($('#viewer_tpl').html()), 

		events: {
			"click .innerBox": "choiceSwap",
			"click #backAPage": "backAPage"
		},

		choiceSwap: function(e){
			var pageid = $(e.target).attr("data-id");
			this.model.changePage(parseInt(pageid));
		},

		backAPage: function(){
			var parentPage = this.model.bookMark.get("parentPage");
			this.model.changePage(parentPage);
		},

		initialize: function(){
			var that = this;
			$(".container").html(this.$el);

			this.model = new bookCollection();
			this.model.title = loadedBook.book.title;

			this.listenTo(this.model, "update", this.render);

			for(var i = 0; i < loadedPages.length; i++){
				var tempJSON = {
					id: parseInt(loadedPages[i].jsonID),
					choiceDialog: loadedPages[i].choiceDialog,
					parentPage: parseInt(loadedPages[i].parentID),
					title: loadedPages[i].title,
					content: loadedPages[i].content
				}
				this.model.add(tempJSON);
			}
			this.model.changePage(1);
		},

		render: function(){
			var that = this;
			
			var choices = this.model.getChoicesFor(this.model.bookMark.get('id'));
			var choicesJSON = [];
			for(var i = 0; i < choices.length; i ++) {
				choicesJSON.push(choices[i].toJSON());
			}
			var tempJSON = {
				link: "42",
				title: this.model.title,
				pageTitle: this.model.bookMark.get("title"),
				content: this.model.bookMark.get("content"),
				choices: choicesJSON
			}

			this.$el.html(this.template(tempJSON));
			return this;
		}

	});

	var bookCollection = Backbone.Collection.extend({
		title: "title",
		model: page,
		bookMark: -1,

		initialize: function(){
			this.on("remove", function(){that.trigger("update")});
			this.on("change", function(){
				this.syncToServer();
			});
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