$(document).ready(function() {
  $(".dropdown-toggle").click(function() {
    $(this).toggleClass("open");
	$(this).next('.dropdown').toggle();
    $('.menu').slideToggle(1000, function(){
      if($(this).css('display') === "none"){
        $(this).removeAttr('open');
      }
    });
  });
});
