$(function(){
	$('.one').click(function(){
		changeWritingArea(optionThreeText);
		emptyOptionArea();
		$("#library").show();
	});


	$('.two').click(function(){
		window.location = "beta.html";
	});

	$('.three').click(function(){
		changeWritingArea(optionFourTest);
		emptyOptionArea();
	});
});



function changeWritingArea(text){
	$("#writingArea").html("<p>"+text+"</p>");
}

function emptyOptionArea(){
	$("#nextChoices > .innerBox").html("");
}

function addOption(text, optionName, callBack){
	$("#nextChoices > .innerBox").append('<div class="index option '+optionName+'">'+text+'</div>');
	$('.'+optionName).click(callBack);
};

var optionThreeText = "ChooseYourOwnAdventure.in is currently in a very limited Alpha stage -- but that doesn't mean you can't go on an adventure!<br><br>"+
"We currently have one Adventure that is worth trying out. But, soon there will be hundreds, thousands, or maybe even more than that! (Optimism!)";

var optionFourTest = "ChooseYourOwnAdventure.in (or: cyoa.in) is small adventure in of itself. Created by Tyreil Poosri, and Seth Gossler, CYOA is a means to not only express ourselves (that's right, we're writing this), but to also have fun making something that other people can use."+
"<br><br>"+
"The main purpose of CYOA isn't for us to share our stories with you, but for you to share your ideas with each other. The soon-to-come CYOA 'Adventure Creator' will allow you and your friends to share Adventures with each other -- it'll be fun, we promise!"+
"<br><br>"+
'Along with personalized home pages, and public "Adventure libraries", we hope to make CYOA a friendly place to create, and experience Adventures with other people.'+
"<br><br>"+
"Hopefuly CYOA will grow, turn into it's own organic machine with hundreds of people sharing stories, and it'll be a place to be online. But for now, Tyreil and Seth would just like to thank you for your interest in CYOA, and we hope you'll be back!";