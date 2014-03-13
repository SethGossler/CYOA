

var page = Backbone.Model.extend({

	defaults: {
		id: 0,
		choiceDialog:'place holder',
		parentID: 0,
		title: 'title',
		content: 'content',
		choices: undefined //These variables are shared to each spawned model(or it seems to be). Need to declare array at init
	},

	initialize: function(){
		if(!this.get('choices'))
		{
			this.set({choices: new Array()});
		}
	},

	addChoiceID: function(pageID){
		var myChoices = this.get('choices');
		myChoices.push(pageID);
		this.set({choices: myChoices})
	}

});