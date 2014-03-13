//CYOA Viewer
//WORK IN PROGRESS 
//-Loads a "properBook" and displays it.

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

		events: {
			"blur .storyText":"contentChanged"
		},

		contentChanged: function(){
			this.model.updatePages();
		},

		initialize: function(){
			var that = this;

			/*loading up the pre-loaded books data*/
			var newBook = new book();
			this.model = newBook;

			$('#writingArea').append(that.render().el);

			/*Re-rendering handlers*/
			this.listenTo(this.model, 'refresh', this.refreshBook);

			/*JQuery handling of button presses*/
			$('#addChoice').click(function(){
				that.model.addPage();
				that.model.updatePages();
			});

			$('#backAPage').click(function(){
				console.log("here");
				if(that.model.bookMark > 0)
				{
					console.log(that.model.currentPage());
					that.model.changePage(that.model.currentPage().get('parentID'));
				}
				that.model.updatePages();
				that.render();
			});

			$('#overViewShow').click(function(){
				alert("This is going to be a much, much later feature ... ");
			});

		},

		render: function(){
			var that = this;

			/*Here, I did a quick fix to add in <br> tags for newlines*/
			var tempJSON = this.model.currentPage().toJSON();
			tempJSON.content = tempJSON.content.replace(/\n/g, '<br>');

			this.$el.html(this.template(tempJSON));

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

	/*bookIndexer - for story creation and editing*/
	var bookIndexer = Backbone.Model.extend({
		model: {},
		url: function(){
			return '/sync/indexer/';
		},

		initialize: function() {

		},

		syncToServer: function() {
			$readLink = $("#readLink");
			var response = this.save(this.model,{
				success:function(model, response, options){
					/*
					*'action' is an attribute I made up, sent back from the
					*as a response. Im sure it'll be useless eventually.
					*/
					if(response.action == "save")
					{
						console.log("saved" + response.id);
						model.set({id:response.id});
						$readLink.html('<a href="http://cyoa.seth.today/readID/'+response.id+'">Link Here!</a>');
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

		url: '/app',

		initialize: function(){
			this.addPage({
				title: "Your first page!",
				content: "This is your first page. Go ahead, add more, and then come back!"
			});
			this.bookMark = this.length-1;
			

			this.indexer = new bookIndexer();
			this.indexer.model = this;
		},

		/*Load Book*/
		/*
		*Takes in a properBook, adds all the pages to the new book
		*and changes the page to the first page. 
		*-So far, this is used for loading a saved book.
		*/
		loadBook: function(properBook) {

			/*add all the pages to "book"*/
			var pages = properBook.pages;
			for(var i = 0; i < pages.length; i++)
			{
				this.addPage(pages[i]);
			}	

			var firstPage = pages[0];

			/*This was quick add, just so the viewer had a "main title"
			-But this still needs to be formalized ... think: why is loadBook handleing this?
			*/
			$('#bookTitle').html(firstPage.title);

			this.changePage(0);
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
			return page;
		},

		addPage: function(details){
			/*addPage
			*-Occurs when the user adds a new "choice" option.
			*-The new pages is the child of the current page, and the choiceView is updated to show the new "choice".
			*/
			var newID = this.length;
			if(details)
			{
				var newPage = new page(details);
			}
			else
			{
				var newPage = new page();
				newPage.set({parentID: this.bookMark});
			}

			newPage.set({id: newID});

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

		/*define change page -- start thinking about object modules...?*/
		changePage: function(id){
			this.bookMark = id;//update currentPage id

			for(var i = 0; i < this.curChoices.length; i++)
			{
				this.curChoices[i].remove();
			}

			var choices = this.currentPage().get('choices');

			for(var i = 0; i < choices.length; i++)
			{
				var currentLoadedChoice = this.at(choices[i])
				var newChoiceView = new choiceView({model:currentLoadedChoice});
				this.curChoices.push(newChoiceView);
				$('#nextChoices').find('.innerBox').append(newChoiceView.render().el);
			}
			
			//"We changed pages. Re-render?"
			this.trigger('refresh');
		}
	});


//removed loadedBook
	var cyoaApp = new appView();
	cyoaApp.render();
});