<html>
<body>


<?php
include("global.inc.php");
pt_register('POST','Subject');
pt_register('POST','Message');
pt_register('POST','Regard');
pt_register('POST','Sender');


//Open the mailinglist and then convert it to a string that can be used
$fh = file_get_contents("list.txt");
$list = str_replace(" ","; ",$fh);
$MailingList = $list;



$html_begin = "<html><head><img src='http://www.yourwebsite.com/images/logo.jpg' align='middle' /></head><body><p>";
$html_end = "</p><br /><br /><a href='http://www.yourwebsite.com/textmailer/remove.html'>Click Here</a> if you want to be taken off our mailing list.</body></html>";

$Message_Full = "$html_begin $Message</p><p>$Regard,<br />$Sender $html_end"; 

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
// More headers, this one is for where you want the reciever so see where the email is from
$headers .= 'From: You At Your Website' . "\r\n";

mail($MailingList,$Subject,$Message_Full,$headers);
echo "Thank You, you message has been sent! <a href='http://www.yoursite.com/'>Click Here to go to the homepage</a>";}


/*
To set up a username/password, change ALL OF THE TEXT into this:
<?php
include("global.inc.php");
pt_register('POST','Username');
pt_register('POST','Password');
pt_register('POST','Subject');
pt_register('POST','Message');
pt_register('POST','Regard');
pt_register('POST','Sender');


//Open the mailinglist and then convert it to a string that can be used
$fh = file_get_contents("list.txt");
$list = str_replace(" ","; ",$fh);
$MailingList = $list;
// For testing: 


//Test if the username is correct
$username_true = "admin";
$password_true = "admin";
$html_begin = "<html><head><img src='http://www.yourwebsite.com/images/logo.jpg' align='middle' /></head><body><p>";
$html_end = "</p><br /><br /><a href='http://www.yourwebsite.com/textmailer/remove.html'>Click Here</a> if you want to be taken off our mailing list.</body></html>";

$Message_Full = "$html_begin $Message</p><p>$Regard,<br />$Sender<br />$Title $html_end"; 

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
// More headers, this one is for where you want the reciever so see where the email is from
$headers .= 'From: You At Your Website' . "\r\n";

if ($Username==$username_true && $Password==$password_true)
{mail($MailingList,$Subject,$Message_Full,$headers);
echo "Thank You, you message has been sent! <a href='http://www.yoursite.com/'>Click Here to go to the homepage</a>";}
else {echo "Your Username and Password combination is incorrect, please go back and try again.";}

*/
/*
If you want to allow multiple users to be able to login, get the username and password combination he or she wants and place it as such after 
	$username_true = "admin";
	$password_true = "admin";
		 '$username_(new) = "(desired username)";
		  $password_(new) = "(desired password)";'
		  
Then, change 'if ($Username==$username_true && $Password==$password_true)' to: 
	'if (($Username==$username_true && $Password==$password_true) or ($Username==$username_(new) && $Password==$password_(new))) '
If you so want, you can add multple users, just add " or ($Username==$username_(new) && $Password==$password_(new))" before the last parenthesis. 

NOTE: MAKE SURE YOU CHANGE ANYWHERE WHERE IT SAYS "(new)" TO A DIFFERENT NAME, INDIVIDUAL TO EACH USER, OR ERRORS WILL OCCUR! 
*/


?>
</body>
</html>
