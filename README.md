#Description
This simple tool connects to an ASUS router running the Download Master app. The features so far:
* add a new torrent by link (magnet links accepted) (get.php)
* check the list of current downloads and remove the finished ones, plus send a notification email (checkDone.php)
* check for new episodes of TV shows (using showrss.info) and add them to the download list (checkNew.php)
* get different levels of debug info by using parameters to checkNew.php and checkDone.php: none (default), info (use the "-d" param) and verbose ("-v")

#Requirements
You need to have a machine with an access to your ASUS router. The machine has to have PHP 5 installed along with SimpleXML. In order to check for new episodes of the shows it needs to have access to the Internet (duh).

#Installation
In order to make the whole thing work, edit the Config.php file filling in your:
* router's IP                                                          
* showrss.info feed URL                                                          
* email address to send notifications to (leave blank for no email)                                   
* HTTP basic auth key (if in doubt, go to your Download Master page and check the requests your browser sends to it -- each of them will have a header saying "Authorization:Basic XXX" - the XXX part is your key)
* writable location where the tool can save its check file

... and you're pretty much good to go! Now just log in to the machine where the tool is installed and fire "php get.php [torrent link]", sit back and watch the magic happen.                                                                                   

An optional (recommended?) step: set up a cron to execute both checkNew.php and checkDone.php, and forget about manually downloading shows ever again!                                                                                                                                                                                                                  
#Disclaimer                                                                                                                                                                          
* as the whole thing is just a simple tool I wrote to save me some headaches, it could be a bit more user-friendly and configurable
* it's also not very idiot-proof; some sanity checks here and there would be nice
* comments and feature requests are welcome!
* I didn't bother to add phpDoc, sorry...
