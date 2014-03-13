$(function(){

	var choiceView = Backbone.View.extend({
		model: page,

		template: _.template($('#choiceTemplate').html()), 

		events: {
			"click .index":"indexPicked",
			"blur .dialog":"edit"
		},

		render: function(){
			var nextChoice = $('#nextChoices').find('.innerBox');
			nextChoice = nextChoice.children().length;
			this.model.set({choiceIndex: nextChoice});
			this.$el.html(this.template(this.model.toJSON()));
			this.input = this.$('.dialog');
			return this;
		},

		indexPicked: function(){
			cyoaApp.model.changePage(this.model.get('id'));
		},

		edit: function(){
			this.model.set({choiceDialog: this.input.val()});
		}

	});


	var appView = Backbone.View.extend({
		
		model: book,
		currentPage: page,

		template:  _.template($('#page').html()), 

		initialize: function(){
			var that = this;
			var newBook = new book();
			this.model = newBook;

			$('#writingArea').append(that.render().el);

			/*Re-rendering handlers*/
			this.listenTo(this.model, 'refresh', this.refreshBook);


			/*JQuery handling of button presses*/
			$('#addChoice').click(function(){
				that.model.addPage();
			});

			$('#backAPage').click(function(){
				if(that.model.bookMark > 0)
				{
					that.model.changePage(that.model.currentPage().get('parentID'));
				}
				that.render();
			});

			$('#overViewShow').click(function(){
				this.show();
			});
		},

		render: function(){
			var that = this;

			this.$el.html(this.template(this.model.currentPage().toJSON()));

			return this;
		},

		/*refreshBook
		* -To handle saving the current book to the server
		- -To handle re-rendering the book when the page changes.
		*/
		refreshBook: function(){
			//console.log("refreshing book");
			this.model.syncToServer();
			this.render();
		}
	});

	var bookIndexer = Backbone.Model.extend({
		model: {},
		url: function(){
			return '/sync/indexer';
		},

		syncToServer: function() {
			var response = this.save(this.model,{
				success:function(model, response, options){
					/*
					*'action' is an attribute I made up, sent back from the
					*as a response. Im sure it'll be useless eventually.
					*/
					if(response.action == "save")
					{
						console.log("saved");
						model.set({id:response.id});
						$('#readLink').html("<a href=http://www.cyoa.in/read/"+response.id+">Book Link</a>");
					}
					if(response.action == "update")
					{
						console.log("updated");
					}

				},
				error:function(model, response, options){
					console.error("Could not save to the server.")
					console.error(response);
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
				id: this.id
			};
			//console.log(properBook);
		    return properBook;
		}
	});

	var book = Backbone.Collection.extend({
		model: page,
		bookMark: -1,
		curChoices: [], //this is an array that holds the 'page' numbers of it's corresponding choices -- look to those for the "choiceDialog"

		//an object to place the book into, which gets sent to the server.
		indexer: {}, 

		url: '/',

		initialize: function(){
			this.addPage({
				title: "Your first page!",
				content: "This is your first page. Go ahead, add more, and then come back!"
			});

			this.indexer = new bookIndexer();
			this.indexer.model = this;

			this.bookMark = this.length-1;
		},

		updatePages: function(){
			var curTitle = $('.title').val();
			var curContent = $('.storyText').val();
			this.currentPage().set({title: curTitle, content: curContent});
		},

		syncToServer: function() {
			this.indexer.syncToServer();
		},

		currentPage: function(){
			var page = this.at(this.bookMark);
			//console.log(page.toJSON());
			return page;
		},

		addPage: function(details){
			var newID = this.length;
			if(details)
			{
				var newPage = new page(details);
			}
			else
			{
				var newPage = new page({title: 'new page: '+newID, content: ''});
			}

			newPage.set({id: newID});
			newPage.set({parentID: this.bookMark});

			this.add(newPage);
			

			/*if the page being added isn't the first page...*/
			if(this.bookMark > -1)
			{
				this.currentPage().addChoiceID(newID);

				var newChoiceView = new choiceView({model: newPage});
				this.curChoices.push(newChoiceView);
				$("#nextChoices").find(".innerBox").append(newChoiceView.render().el);
			}

			return newPage;
			//console.log(this.length);
		},

		changePage: function(id){
			this.updatePages();

			this.bookMark = id;

			for(var i = 0; i < this.curChoices.length; i++)
			{
				this.curChoices[i].remove();
			}

			var choices = this.currentPage().get('choices');

			//Here we go through the choices of the new page, get the pages referenced in the choices, and  
			//add their 'dialog' to the nextChoices div.
			for(var i = 0; i < choices.length; i++)
			{
				var curChoicePage = this.at(choices[i])
				console.log(curChoicePage);
				var newChoiceView = new choiceView({model:curChoicePage});
				this.curChoices.push(newChoiceView);
				$('#nextChoices').find('.innerBox').append(newChoiceView.render().el);
			}

			//"We changed pages. Re-render?"
			this.trigger('refresh');
		}


	});

	var page = Backbone.Model.extend({

		defaults: {
			id: 0,
			choiceDialog:'place holder',
			parentID: 0,
			title: 'title',
			content: 'content',
			choices: [],
		},

		initialize: function(){
			this.set({choices: new Array()});
			//console.log("page made");
		},

		addChoiceID: function(pageID){
			console.log("add ");
			var curChoices = this.get('choices');
			curChoices.push(pageID);
			this.set({choices: curChoices})
		}

	});



	var cyoaApp = new appView();
	cyoaApp.render();
});