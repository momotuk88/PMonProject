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
//Живой поиск
$(function(){
$('.query').keyup(function() {
    if(this.value.length >= 2){
        $.ajax({
            url: root+"ajax/search.php",
			type: "POST",
			data:{zapros:this.value},
			dataType:"text",
            success: function(data){
                $("#mainstats").html(data).fadeIn(); //Выводим полученые данные в списке
           }
       })
    }	
})   
})
