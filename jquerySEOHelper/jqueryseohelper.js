$(document).ready(function(){
  var thename;
  var theid;
  var thealt;
  var thetitle;

  $('*').each(function() {
    if($(this).attr("name"))
      {
        thename=$(this).attr("name");
        $(this).attr("id",thename);
      }
  });
  
  $('*').each(function() {
    if($(this).attr("id"))
      {
        theid=$(this).attr("id");
        $(this).attr("name",theid);
      }
    });
    
  $('img').each(function() {
    if($(this).attr("alt"))
      {
        thealt=$(this).attr("alt");
        $(this).attr("title",thealt);
      }
    });
    
  $('img').each(function() {
    if($(this).attr("title"))
      {
        thetitle=$(this).attr("title");
        $(this).attr("alt",thetitle);
      }
    });
});
