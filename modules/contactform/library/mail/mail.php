<?php



include "libmail.php";

$m= new Mail; // create the mail

$m->From( "leo@isp.com" );

$m->To( "develop@shasama.com" );

$m->Subject( "the subject of the mail" );

$m->Body( '<html>

		 <body>

		 Hello\nThis is a test of the Mail component<br>

		 <font color=red>This é à tord é à ça l\'heure</font><br>

		 <table style="border:1px solid green">

		 <tr><td>1</td><td>2</td></tr>

		 </table>

		 </body>

		 </html>' ); 	// set the body



/*$m->Cc( "someone@somewhere.fr");

$m->Bcc( "someoneelse@somewhere.fr");

*/

$m->Priority(1) ; 	// set the priority to Low

$m->Attach( "text1.txt", "text/plain", "attachment" ) ; 	// attach a file of type image/gif to be displayed in the message if possible

$m->Attach( "text2.txt", "text/plain", "attachment" ); 

$m->Send(); 	// send the mail



echo "Mail was sent:";

echo $m->Get(); // show the mail source





?>