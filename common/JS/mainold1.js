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
			this.listenTo(this.model, 'change', this.render);


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

			//console.log("render");

			this.$el.html(this.template(this.model.currentPage().toJSON()));
			
			//console.log(choices);
			//console.log(this.model.currentPage());

			//$('#nextChoices').children().remove();

			return this;
		},

		/*This is to save to the server ??*/
		saveAll: function(){
			console.log("saveAll: Request to save.");
			this.model.save();
			this.render();
		}
	});

	var book = Backbone.Collection.extend({
		model: page,
		bookMark: -1,
		curChoices: [],

		initialize: function(){
			this.addPage({
				title: "Your first page!",
				content: "This is your first page. Go ahead, add more, and then come back!"
			});

			this.bookMark = this.length-1;
		},

		save: function(){
			console.log("saved");
			var curTitle = $('.title').val();
			var curContent = $('.storyText').val();
			this.currentPage().set({title: curTitle, content: curContent});
			//Let the app controller know changed our data.
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
			this.save();

			this.bookMark = id;

			for(var i = 0; i < this.curChoices.length; i++)
			{
				this.curChoices[i].remove();
			}

			var choices = this.currentPage().get('choices');

			for(var i = 0; i < choices.length; i++)
			{
				var curChoicePage = this.at(choices[i])
				var newChoiceView = new choiceView({model:curChoicePage});
				this.curChoices.push(newChoiceView);
				$('#nextChoices').find('.innerBox').append(newChoiceView.render().el);
			}

			//"We changed pages. Re-render?"
		}


	});

	var page = Backbone.Model.extend({

		defaults: {
			id: 0,
			choiceDialog:'place holder',
			parentID: 0,
			title: 'title',
			content: 'content',
			choices: []
		},

		initialize: function(){
			this.set({choices: new Array()});
			//console.log("page made");
		},

		addChoiceID: function(pageID){
			var curChoices = this.get('choices');
			curChoices.push(pageID);
			this.set({choices: curChoices})
		}

	});



	var cyoaApp = new appView();
	cyoaApp.render();
});